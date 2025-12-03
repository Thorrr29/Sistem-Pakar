"""
FastAPI-based hybrid ML + fuzzy expert system for DENGUE diagnosis.

Engine ini:
- Meload dataset klinis dengue dari:
    storage/app/public/Dengue_clinical_dataset.csv
- Melatih dua model ML (LogisticRegression & RandomForest)
- Memilih model terbaik berdasarkan akurasi test
- Membangun fuzzy expert system di atas probabilitas kelas positif
- Menyediakan endpoint /infer untuk inferensi 1 pasien

Request contoh:
POST /infer
{
  "features": {
    "Gender": "Male",
    "Age": 16,
    "Platelet Count": 149134,
    "WBC": 4468,
    "Location": "Keraniganj",
    "Fever": true,
    "Duration_of_Fever": 5,
    "Headache": false,
    "Muscle_Pain": true,
    "Rash": false,
    "Vomiting": true
  }
}

Response berisi:
- label prediksi ML
- probabilitas per kelas
- nilai & level kepercayaan fuzzy (weak / moderate / strong)
"""

from __future__ import annotations

from pathlib import Path
from typing import Any, Dict, Optional

import numpy as np
import pandas as pd
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field

from sklearn.compose import ColumnTransformer
from sklearn.ensemble import RandomForestClassifier
from sklearn.impute import SimpleImputer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import (
    accuracy_score,
    classification_report,
    f1_score,
    precision_score,
    recall_score,
)
from sklearn.model_selection import train_test_split
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder, StandardScaler, LabelEncoder

import skfuzzy as fuzz
from skfuzzy import control as ctrl


# ---------------------------------------------------------------------------
# Konfigurasi path & global state
# ---------------------------------------------------------------------------

BASE_DIR = Path(__file__).resolve().parents[1]
DATA_PATH = BASE_DIR / "storage" / "app" / "public" / "Dengue_clinical_dataset.csv"
# File laporan metrik model akan disimpan di folder yang sama dengan main.py
METRICS_PATH = Path(__file__).with_name("dengue_model_metrics.txt")

RANDOM_STATE = 42
np.random.seed(RANDOM_STATE)

app = FastAPI(
    title="Dengue Diagnosis Engine - ML + Fuzzy",
    description="Hybrid machine learning + fuzzy expert system untuk diagnosis dengue.",
    version="2.0.0",
)


class DengueInferRequest(BaseModel):
    """
    Map nama fitur -> nilai, mengikuti header di CSV
    (tanpa kolom 'Outcome').

    Contoh:
    {
      "features": {
        "Gender": "Male",
        "Age": 16,
        "Platelet Count": 149134,
        "WBC": 4468,
        "Location": "Keraniganj",
        "Fever": true,
        "Duration_of_Fever": 5,
        "Headache": false,
        "Muscle_Pain": true,
        "Rash": false,
        "Vomiting": true
      }
    }
    """

    features: Dict[str, Any] = Field(
        ...,
        description=(
            "Dictionary fitur klinis dengue. "
            "Key wajib minimal sama dengan kolom input di dataset "
            "(kecuali Id dan Outcome)."
        ),
    )


class DengueInferResponse(BaseModel):
    predicted_label: str
    predicted_label_encoded: int
    probabilities: Dict[str, float]
    positive_class_label: str
    p_positive: float
    p_negative: float
    fuzzy_confidence_score: float
    fuzzy_confidence_level: str
    explanation: str


# ---------------------------------------------------------------------------
# Global model & fuzzy objects (diinisialisasi saat startup)
# ---------------------------------------------------------------------------

preprocessor: Optional[ColumnTransformer] = None
label_encoder: Optional[LabelEncoder] = None
best_model: Optional[Pipeline] = None
best_model_name: Optional[str] = None
expected_feature_columns: Optional[list[str]] = None
positive_class_label: Optional[str] = None
positive_class_encoded: Optional[int] = None
confidence_ctrl: Optional[ctrl.ControlSystem] = None

MODEL_READY: bool = False
MODEL_ERROR: Optional[str] = None


def _detect_target_column(df: pd.DataFrame) -> str:
    """Deteksi kolom target (label) dari dataset dengue."""
    possible_target_candidates = [
        "label",
        "target",
        "diagnosis",
        "dengue",
        "outcome",
        "class",
    ]

    for col in df.columns:
        col_lower = col.lower()
        if (
            col_lower in possible_target_candidates
            or "dengue" in col_lower
            or "diagnos" in col_lower
            or "outcome" in col_lower
            or col_lower == "class"
        ):
            return col

    # fallback: gunakan kolom terakhir
    return df.columns[-1]


def _build_fuzzy_system() -> ctrl.ControlSystem:
    """Bangun fuzzy control system untuk DiagnosisConfidence."""
    prob_universe = np.arange(0, 1.01, 0.01)

    # Fuzzy input variables
    P_Positive = ctrl.Antecedent(prob_universe, "P_Positive")
    P_Negative = ctrl.Antecedent(prob_universe, "P_Negative")

    # Fuzzy output variable
    DiagnosisConfidence = ctrl.Consequent(prob_universe, "DiagnosisConfidence")

    # Membership functions untuk probabilitas
    for var in [P_Positive, P_Negative]:
        var["low"] = fuzz.trimf(var.universe, [0.0, 0.0, 0.4])
        var["medium"] = fuzz.trimf(var.universe, [0.2, 0.5, 0.8])
        var["high"] = fuzz.trimf(var.universe, [0.6, 1.0, 1.0])

    # Membership untuk confidence
    DiagnosisConfidence["weak"] = fuzz.trimf(
        DiagnosisConfidence.universe, [0.0, 0.0, 0.4]
    )
    DiagnosisConfidence["moderate"] = fuzz.trimf(
        DiagnosisConfidence.universe, [0.2, 0.5, 0.8]
    )
    DiagnosisConfidence["strong"] = fuzz.trimf(
        DiagnosisConfidence.universe, [0.6, 1.0, 1.0]
    )

    # Fuzzy rules
    rule1 = ctrl.Rule(P_Positive["high"], DiagnosisConfidence["strong"])
    rule2 = ctrl.Rule(
        P_Positive["medium"] & P_Negative["low"], DiagnosisConfidence["strong"]
    )
    rule3 = ctrl.Rule(
        P_Positive["medium"] & P_Negative["medium"], DiagnosisConfidence["moderate"]
    )
    rule4 = ctrl.Rule(
        P_Positive["medium"] & P_Negative["high"], DiagnosisConfidence["moderate"]
    )
    rule5 = ctrl.Rule(
        P_Positive["low"] & P_Negative["high"], DiagnosisConfidence["weak"]
    )
    rule6 = ctrl.Rule(
        P_Positive["low"] & P_Negative["medium"], DiagnosisConfidence["weak"]
    )
    rule7 = ctrl.Rule(
        P_Positive["low"] & P_Negative["low"], DiagnosisConfidence["moderate"]
    )

    return ctrl.ControlSystem(
        [rule1, rule2, rule3, rule4, rule5, rule6, rule7]
    )


def load_and_train_model() -> None:
    """
    Load dataset dengue, latih model, dan siapkan fuzzy system.
    Dipanggil sekali saat startup (module import).
    """
    global preprocessor, label_encoder, best_model, best_model_name
    global expected_feature_columns, positive_class_label, positive_class_encoded
    global confidence_ctrl, MODEL_READY, MODEL_ERROR

    try:
        if not DATA_PATH.exists():
            raise FileNotFoundError(f"Dataset not found at: {DATA_PATH}")

        df = pd.read_csv(DATA_PATH)

        # Deteksi target/label
        target_col = _detect_target_column(df)

        X = df.drop(columns=[target_col])
        y = df[target_col]

        # Buang kolom ID jika ada
        for id_col in ["Id", "ID", "id"]:
            if id_col in X.columns:
                X = X.drop(columns=[id_col])

        # Simpan nama fitur yang diharapkan
        expected_feature_columns = list(X.columns)

        # Deteksi tipe fitur
        numeric_cols = X.select_dtypes(
            include=["int64", "float64", "int32", "float32"]
        ).columns.tolist()
        categorical_cols = X.select_dtypes(
            include=["object", "bool", "category"]
        ).columns.tolist()

        # Encoder untuk label
        label_encoder = LabelEncoder()
        y_encoded = label_encoder.fit_transform(y)

        # Preprocessing pipeline
        numeric_transformer = Pipeline(
            steps=[
                ("imputer", SimpleImputer(strategy="median")),
                ("scaler", StandardScaler()),
            ]
        )

        categorical_transformer = Pipeline(
            steps=[
                ("imputer", SimpleImputer(strategy="most_frequent")),
                # scikit-learn >= 1.2 uses `sparse_output` instead of `sparse`
                ("onehot", OneHotEncoder(handle_unknown="ignore", sparse_output=False)),
            ]
        )

        transformers = []
        if numeric_cols:
            transformers.append(("num", numeric_transformer, numeric_cols))
        if categorical_cols:
            transformers.append(("cat", categorical_transformer, categorical_cols))

        preprocessor = ColumnTransformer(transformers=transformers)

        # Train/test split
        X_train, X_test, y_train, y_test = train_test_split(
            X,
            y_encoded,
            test_size=0.2,
            stratify=y_encoded,
            random_state=RANDOM_STATE,
        )

        # Dua model kandidat
        models = {
            "LogisticRegression": LogisticRegression(
                max_iter=1000,
                class_weight="balanced",
                random_state=RANDOM_STATE,
            ),
            "RandomForest": RandomForestClassifier(
                n_estimators=300,
                max_depth=None,
                class_weight="balanced",
                random_state=RANDOM_STATE,
            ),
        }

        fitted_models: Dict[str, Pipeline] = {}
        accuracies: Dict[str, float] = {}

        for name, estimator in models.items():
            clf = Pipeline(
                steps=[
                    ("preprocessor", preprocessor),
                    ("model", estimator),
                ]
            )
            clf.fit(X_train, y_train)
            y_pred = clf.predict(X_test)
            acc = accuracy_score(y_test, y_pred)
            fitted_models[name] = clf
            accuracies[name] = acc

        # Pilih model terbaik
        best_model_name = max(accuracies, key=accuracies.get)
        best_model = fitted_models[best_model_name]

        # Hitung metrik detail untuk model terbaik
        y_pred_best = best_model.predict(X_test)
        acc_best = accuracy_score(y_test, y_pred_best)
        precision_best = precision_score(
            y_test, y_pred_best, average="weighted", zero_division=0
        )
        recall_best = recall_score(
            y_test, y_pred_best, average="weighted", zero_division=0
        )
        f1_best = f1_score(
            y_test, y_pred_best, average="weighted", zero_division=0
        )

        cls_report = classification_report(
            y_test,
            y_pred_best,
            target_names=[str(c) for c in label_encoder.classes_],
        )

        # Simpan metrik ke file teks di sebelah main.py
        try:
            lines = [
                "Dengue Model Training Metrics",
                "==============================",
                f"Best model        : {best_model_name}",
                f"Test accuracy     : {acc_best:.4f}",
                f"Precision (weighted): {precision_best:.4f}",
                f"Recall (weighted) : {recall_best:.4f}",
                f"F1-score (weighted): {f1_best:.4f}",
                "",
                "Classification report:",
                cls_report,
                "",
            ]
            METRICS_PATH.write_text("\n".join(lines), encoding="utf-8")
        except Exception as write_exc:  # pragma: no cover - logging saja
            print(
                "[DENGUE ENGINE] WARNING: Gagal menulis file metrik:",
                repr(write_exc),
            )

        # Deteksi kelas "positif dengue"
        positive_keywords = ["dengue", "positive", "pos", "yes", "1", "severe"]
        positive_class_label = None
        for cls in label_encoder.classes_:
            cls_lower = str(cls).lower()
            if any(kw in cls_lower for kw in positive_keywords):
                positive_class_label = cls
                break
        if positive_class_label is None:
            # fallback: kelas dengan index tertinggi
            positive_class_label = label_encoder.classes_[-1]

        positive_class_encoded = int(
            np.where(label_encoder.classes_ == positive_class_label)[0][0]
        )

        # Bangun fuzzy system
        confidence_ctrl = _build_fuzzy_system()

        MODEL_READY = True
        MODEL_ERROR = None

        print(
            f"[DENGUE ENGINE] Model loaded. Best={best_model_name}, "
            f"Accuracy={acc_best:.4f}, "
            f"Positive class='{positive_class_label}'"
        )

    except Exception as exc:  # pragma: no cover - startup error logging
        MODEL_READY = False
        MODEL_ERROR = f"{type(exc).__name__}: {exc}"
        print("[DENGUE ENGINE] ERROR while loading model:", MODEL_ERROR)


# Jalankan training saat module di-import
load_and_train_model()


def _run_fuzzy_inference(p_positive: float, p_negative: float) -> Dict[str, Any]:
    """Jalankan fuzzy inference dan kembalikan skor & level linguistik."""
    if confidence_ctrl is None:
        raise RuntimeError("Fuzzy control system is not initialized.")

    fuzzy_sim = ctrl.ControlSystemSimulation(confidence_ctrl)
    fuzzy_sim.input["P_Positive"] = float(p_positive)
    fuzzy_sim.input["P_Negative"] = float(p_negative)
    fuzzy_sim.compute()

    conf_score = float(fuzzy_sim.output["DiagnosisConfidence"])

    if conf_score < 0.33:
        conf_level = "weak"
    elif conf_score < 0.66:
        conf_level = "moderate"
    else:
        conf_level = "strong"

    return {"score": conf_score, "level": conf_level}


def _prepare_input_row(features: Dict[str, Any]) -> pd.DataFrame:
    """
    Susun 1 baris DataFrame sesuai urutan expected_feature_columns.
    Fitur yang tidak dikirim akan diisi NaN dan ditangani oleh imputer.
    """
    if expected_feature_columns is None:
        raise RuntimeError("Expected feature columns are not initialized.")

    row: Dict[str, Any] = {}
    for col in expected_feature_columns:
        # Gunakan key persis seperti di CSV
        if col in features:
            row[col] = features[col]
        else:
            row[col] = np.nan

    return pd.DataFrame([row])


# ---------------------------------------------------------------------------
# FastAPI endpoints
# ---------------------------------------------------------------------------


@app.get("/", summary="Health check")
async def root() -> Dict[str, Any]:
    """Cek status engine dan ketersediaan model."""
    return {
        "status": "ok" if MODEL_READY else "error",
        "model_ready": MODEL_READY,
        "model_name": best_model_name,
        "positive_class_label": positive_class_label,
        "error": MODEL_ERROR,
    }


@app.post("/infer", response_model=DengueInferResponse, summary="Inferensi dengue (ML + fuzzy)")
async def infer(request: DengueInferRequest) -> DengueInferResponse:
    """
    Terima fitur klinis pasien, jalankan model ML untuk prediksi dengue,
    lalu interpretasikan probabilitas dengan fuzzy expert system.
    """
    if not MODEL_READY or best_model is None or label_encoder is None:
        raise HTTPException(
            status_code=503,
            detail=f"Model is not ready. Error: {MODEL_ERROR}",
        )

    if not request.features:
        raise HTTPException(
            status_code=400,
            detail="Field 'features' tidak boleh kosong.",
        )

    # Susun baris input sesuai urutan fitur saat training
    X_input = _prepare_input_row(request.features)

    # Prediksi probabilitas dan kelas
    proba = best_model.predict_proba(X_input)[0]
    pred_encoded = int(best_model.predict(X_input)[0])
    pred_label = str(label_encoder.inverse_transform([pred_encoded])[0])

    # Mapping ke probabilitas kelas positif
    model_classes = best_model.named_steps["model"].classes_
    if positive_class_encoded is None:
        raise HTTPException(
            status_code=500,
            detail="Positive class encoding is not set.",
        )

    try:
        pos_index_in_proba = list(model_classes).index(positive_class_encoded)
    except ValueError:
        # jika tidak ketemu, fallback: index terakhir
        pos_index_in_proba = len(model_classes) - 1

    p_pos = float(proba[pos_index_in_proba])
    p_neg = float(1.0 - p_pos)

    # Fuzzy inference
    fuzzy_result = _run_fuzzy_inference(p_pos, p_neg)

    # Probabilitas per kelas (nama asli)
    probabilities = {
        str(cls): float(prob)
        for cls, prob in zip(label_encoder.classes_, proba)
    }

    explanation = (
        f"ML model '{best_model_name}' memprediksi '{pred_label}'. "
        f"Probabilitas kelas positif dengue ('{positive_class_label}') = {p_pos:.3f}. "
        f"Fuzzy system menginterpretasikan kepercayaan diagnosis sebagai "
        f"'{fuzzy_result['level']}' (skor {fuzzy_result['score']:.3f}) "
        f"berdasarkan P_Positive dan P_Negative."
    )

    return DengueInferResponse(
        predicted_label=pred_label,
        predicted_label_encoded=pred_encoded,
        probabilities=probabilities,
        positive_class_label=str(positive_class_label),
        p_positive=p_pos,
        p_negative=p_neg,
        fuzzy_confidence_score=fuzzy_result["score"],
        fuzzy_confidence_level=fuzzy_result["level"],
        explanation=explanation,
    )


if __name__ == "__main__":
    import uvicorn

    uvicorn.run("main:app", host="0.0.0.0", port=8001, reload=True)
