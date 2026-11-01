
from pathlib import Path
import json
import sys

import numpy as np
import pandas as pd

from sklearn.compose import ColumnTransformer
from sklearn.dummy import DummyClassifier
from sklearn.ensemble import RandomForestClassifier
from sklearn.impute import SimpleImputer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import (
    accuracy_score,
    balanced_accuracy_score,
    confusion_matrix,
    f1_score,
    make_scorer,
    recall_score,
)
from sklearn.model_selection import StratifiedGroupKFold, cross_validate
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.tree import DecisionTreeClassifier


ROOT_DIR = Path(__file__).resolve().parents[2]

DATA_FILE = (
    ROOT_DIR
    / "ml/data/processed/dataset_prediksi_stunting.csv"
)

REPORT_DIR = ROOT_DIR / "ml/reports"

REPORT_FILE = (
    REPORT_DIR
    / "model_comparison.json"
)

FEATURE_COLUMNS = [
    "umur_hari_awal",
    "jenis_kelamin",
    "berat_badan_awal_kg",
    "tinggi_badan_awal_cm",
    "haz_awal",
    "waz_awal",
    "whz_awal",
    "baz_awal",
    "jarak_pemeriksaan_sebelumnya_hari",
    "perubahan_berat_badan_kg",
    "perubahan_tinggi_badan_cm",
    "perubahan_haz",
]

TARGET_COLUMN = "label_risiko"
GROUP_COLUMN = "group_id"

EXPECTED_CLASSES = {
    "rendah",
    "sedang",
    "tinggi",
}


def load_dataset() -> pd.DataFrame:
    if not DATA_FILE.exists():
        raise FileNotFoundError(
            f"Dataset tidak ditemukan: {DATA_FILE}"
        )

    return pd.read_csv(DATA_FILE)


def validate_dataset(df: pd.DataFrame) -> tuple:
    if df.empty:
        raise ValueError(
            "Dataset kosong. Training belum dapat dilakukan."
        )

    required = (
        FEATURE_COLUMNS
        + [TARGET_COLUMN, GROUP_COLUMN]
    )

    missing = [
        col for col in required
        if col not in df.columns
    ]

    if missing:
        raise ValueError(
            f"Kolom berikut tidak ditemukan: {missing}"
        )

    df = df.copy()

    df[TARGET_COLUMN] = (
        df[TARGET_COLUMN]
        .astype("string")
        .str.strip()
        .str.lower()
    )

    df[GROUP_COLUMN] = (
        df[GROUP_COLUMN]
        .astype("string")
        .str.strip()
    )

    df = df.dropna(
        subset=[TARGET_COLUMN, GROUP_COLUMN]
    )

    df = df[
        df[TARGET_COLUMN].isin(EXPECTED_CLASSES)
        & df[GROUP_COLUMN].ne("")
    ].copy()

    if df.empty:
        raise ValueError(
            "Tidak ada baris dengan label dan group_id valid."
        )

    return df


def check_training_readiness(df: pd.DataFrame) -> int:
    label_counts = df[TARGET_COLUMN].value_counts()

    missing_classes = (
        EXPECTED_CLASSES - set(label_counts.index)
    )

    if missing_classes:
        raise ValueError(
            "Training belum dapat dilakukan. "
            f"Kelas belum tersedia: {sorted(missing_classes)}"
        )

    groups_per_class = (
        df.groupby(TARGET_COLUMN)[GROUP_COLUMN]
        .nunique()
    )

    min_groups = int(groups_per_class.min())

    # Minimal dua kelompok anak per kelas
    # agar validasi silang berbasis kelompok dapat dilakukan.
    if min_groups < 2:
        raise ValueError(
            "Training belum dapat dilakukan. "
            "Setiap kelas membutuhkan minimal dua kelompok anak "
            "yang berbeda untuk validasi silang."
        )

    n_splits = min(5, min_groups)

    if df[GROUP_COLUMN].nunique() < n_splits:
        raise ValueError(
            "Jumlah anak unik belum mencukupi untuk validasi silang."
        )

    return n_splits


def recall_risiko_tinggi(y_true, y_pred):
    return recall_score(
        np.asarray(y_true) == "tinggi",
        np.asarray(y_pred) == "tinggi",
        zero_division=0,
    )


def build_preprocessor(
    numeric_features: list,
    categorical_features: list,
    scale_numeric: bool,
) -> ColumnTransformer:

    numeric_steps = [
        (
            "imputer",
            SimpleImputer(
                strategy="median",
                keep_empty_features=True,
            ),
        )
    ]

    if scale_numeric:
        numeric_steps.append(
            ("scaler", StandardScaler())
        )

    numeric_pipeline = Pipeline(numeric_steps)

    categorical_pipeline = Pipeline([
        (
            "imputer",
            SimpleImputer(
                strategy="most_frequent",
            ),
        ),
        (
            "encoder",
            OneHotEncoder(
                handle_unknown="ignore",
            ),
        ),
    ])

    transformers = []

    if numeric_features:
        transformers.append(
            ("numeric", numeric_pipeline, numeric_features)
        )

    if categorical_features:
        transformers.append(
            (
                "categorical",
                categorical_pipeline,
                categorical_features,
            )
        )

    return ColumnTransformer(
        transformers=transformers,
        remainder="drop",
    )


def build_models(
    numeric_features: list,
    categorical_features: list,
) -> dict:

    models = {}

    # Logistic Regression
    models["Logistic Regression"] = Pipeline([
        (
            "preprocessor",
            build_preprocessor(
                numeric_features,
                categorical_features,
                scale_numeric=True,
            ),
        ),
        (
            "classifier",
            LogisticRegression(
                max_iter=2000,
                class_weight="balanced",
            ),
        ),
    ])

    # Decision Tree
    models["Decision Tree"] = Pipeline([
        (
            "preprocessor",
            build_preprocessor(
                numeric_features,
                categorical_features,
                scale_numeric=False,
            ),
        ),
        (
            "classifier",
            DecisionTreeClassifier(
                max_depth=5,
                min_samples_leaf=3,
                class_weight="balanced",
                random_state=42,
            ),
        ),
    ])

    # Random Forest
    models["Random Forest"] = Pipeline([
        (
            "preprocessor",
            build_preprocessor(
                numeric_features,
                categorical_features,
                scale_numeric=False,
            ),
        ),
        (
            "classifier",
            RandomForestClassifier(
                n_estimators=200,
                min_samples_leaf=2,
                class_weight="balanced",
                random_state=42,
                n_jobs=1,
            ),
        ),
    ])

    # Baseline
    models["Dummy Classifier"] = Pipeline([
        (
            "preprocessor",
            build_preprocessor(
                numeric_features,
                categorical_features,
                scale_numeric=False,
            ),
        ),
        (
            "classifier",
            DummyClassifier(
                strategy="prior",
            ),
        ),
    ])

    return models


def main() -> int:
    print("=" * 60)
    print("POSYANDU-IN | MODEL COMPARISON")
    print("=" * 60)

    try:
        print("\n[1] Membaca dataset...")

        df = load_dataset()

        print(f"    Jumlah sampel: {len(df)}")

        print("\n[2] Memeriksa kesiapan dataset...")

        df = validate_dataset(df)

        n_splits = check_training_readiness(df)

        print(f"    Dataset valid: {len(df)}")
        print(f"    Jumlah anak unik: {df[GROUP_COLUMN].nunique()}")
        print(f"    Jumlah fold: {n_splits}")

        print("\n[3] Menyiapkan fitur...")

        # Hapus fitur yang seluruh nilainya kosong.
        available_features = [
            col for col in FEATURE_COLUMNS
            if df[col].notna().any()
        ]

        if not available_features:
            raise ValueError(
                "Tidak ada fitur yang dapat digunakan."
            )

        X = df[available_features]
        y = df[TARGET_COLUMN]
        groups = df[GROUP_COLUMN]

        categorical_features = [
            col for col in available_features
            if col == "jenis_kelamin"
        ]

        numeric_features = [
            col for col in available_features
            if col != "jenis_kelamin"
        ]

        print(f"    Jumlah fitur: {len(available_features)}")

        print("\n[4] Menyiapkan validasi silang...")

        cv = StratifiedGroupKFold(
            n_splits=n_splits,
            shuffle=True,
            random_state=42,
        )

        scoring = {
            "accuracy": "accuracy",
            "balanced_accuracy": "balanced_accuracy",
            "f1_macro": "f1_macro",
            "recall_risiko_tinggi": make_scorer(
                recall_risiko_tinggi
            ),
        }

        print("\n[5] Menyiapkan empat model...")

        models = build_models(
            numeric_features,
            categorical_features,
        )

        hasil = {}

        for nama, model in models.items():
            print(f"\n    Mengevaluasi: {nama}")

            scores = cross_validate(
                model,
                X,
                y,
                groups=groups,
                cv=cv,
                scoring=scoring,
                n_jobs=1,
                error_score="raise",
            )

            hasil[nama] = {
                metric: {
                    "mean": float(np.mean(scores[f"test_{metric}"])),
                    "std": float(np.std(scores[f"test_{metric}"])),
                }
                for metric in scoring
            }

        print("\n[6] Menyimpan laporan perbandingan...")

        REPORT_DIR.mkdir(
            parents=True,
            exist_ok=True,
        )

        report = {
            "nama_proyek": "Posyandu-In",
            "periode_prediksi_bulan": 3,
            "jumlah_sampel": len(df),
            "jumlah_anak": int(groups.nunique()),
            "jumlah_fold": n_splits,
            "distribusi_label": {
                label: int(count)
                for label, count in (
                    y.value_counts()
                    .reindex(
                        ["rendah", "sedang", "tinggi"],
                        fill_value=0,
                    )
                    .items()
                )
            },
            "fitur": available_features,
            "hasil_perbandingan": hasil,
            "model_terpilih": None,
            "status": "EVALUASI_AWAL",
            "catatan": (
                "Hasil perbandingan awal belum menjadi "
                "validasi klinis atau dasar diagnosis."
            ),
        }

        with open(
            REPORT_FILE,
            "w",
            encoding="utf-8",
        ) as file:
            json.dump(
                report,
                file,
                indent=4,
                ensure_ascii=False,
            )

        print(f"    Laporan: {REPORT_FILE}")

        print("\nPerbandingan model:")

        for nama, metrik in hasil.items():
            print(f"\n{nama}")

            for metric, values in metrik.items():
                print(
                    f"    {metric}: "
                    f"{values['mean']:.4f} "
                    f"(std {values['std']:.4f})"
                )

        print("\nEvaluasi selesai.")
        print("Model belum dipilih secara otomatis.")

        return 0

    except (
        FileNotFoundError,
        ValueError,
        pd.errors.ParserError,
    ) as error:

        print("\nTRAINING BELUM DAPAT DILAKUKAN")
        print(f"Alasan: {error}")

        return 1


if __name__ == "__main__":
    sys.exit(main())
