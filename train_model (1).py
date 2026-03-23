"""
train_model.py

Train a RandomForest classifier on ml/dataset.csv and save ml/model.pkl (joblib).
Requires: pandas, scikit-learn, joblib

Usage:
    pip install pandas scikit-learn joblib
    python train_model.py
"""
import os
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
import joblib

def main():
    ds = os.path.join(os.path.dirname(__file__), "dataset.csv")
    df = pd.read_csv(ds)
    # Ensure required columns exist
    required = ['N','P','K','temperature','humidity','ph','rainfall','label']
    for c in required:
        if c not in df.columns:
            raise RuntimeError(f"Missing column: {c}")
    df = df[required].dropna()
    X = df[['N','P','K','temperature','humidity','ph','rainfall']].astype(float)
    y = df['label'].astype(str)
    labels = sorted(y.unique())
    label_to_idx = {lab:i for i,lab in enumerate(labels)}
    y_idx = y.map(label_to_idx).astype(int)
    clf = RandomForestClassifier(n_estimators=100, random_state=42)
    clf.fit(X, y_idx)
    out = {'model': clf, 'labels': {i:lab for lab,i in label_to_idx.items()}}
    joblib.dump(out, os.path.join(os.path.dirname(__file__), "model.pkl"))
    print("Trained model saved to ml/model.pkl")
if __name__ == "__main__":
    main()