import sys, os, json
try:
    import joblib, numpy as np
    import pandas as pd
except Exception:
    print(json.dumps({"error":"Required Python packages not installed. Install joblib, numpy, pandas."}))
    sys.exit(0)

def main():
    if len(sys.argv) < 8:
        print(json.dumps({"error":"Expect 7 numeric args: N P K temperature humidity ph rainfall"}))
        return
    try:
        vals = [float(x) for x in sys.argv[1:8]]
    except Exception:
        print(json.dumps({"error":"All inputs must be numeric"}))
        return

    model_file = os.path.join(os.path.dirname(__file__), "model.pkl")
    if not os.path.exists(model_file):
        print(json.dumps({"error":"Model file not found. Run train_model.py to create ml/model.pkl"}))
        return

    data = joblib.load(model_file)
    clf = data['model']
    labels = data['labels']

   
    columns = ["N", "P", "K", "temperature", "humidity", "ph", "rainfall"]
    X = pd.DataFrame([vals], columns=columns)

    try:
        probs = clf.predict_proba(X)
        idx = int(probs.argmax(axis=1)[0])
        confidence = float(probs.max())
        label = labels.get(idx) if isinstance(labels, dict) else labels[idx]
        print(json.dumps({"crop": label, "confidence": round(float(confidence), 4)}))
    except Exception as e:
        print(json.dumps({"error":"Prediction failed: " + str(e)}))

if __name__ == "__main__":
    main()
