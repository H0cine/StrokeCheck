from flask import Flask, request, jsonify
import joblib
import pandas as pd

# SHAP optional
try:
    import shap
    import numpy as np
    SHAP_AVAILABLE = True
except:
    SHAP_AVAILABLE = False

app = Flask(__name__)

model = joblib.load("WEB_stroke_random_forest_model_WEB.joblib")

feature_names = [
    'age','avg_glucose_level','bmi','gender_Male',
    'hypertension_1','heart_disease_1','ever_married_Yes',
    'work_type_Never_worked','work_type_Private',
    'work_type_Self-employed','work_type_children',
    'Residence_type_Urban',
    'smoking_status_formerly smoked',
    'smoking_status_never smoked',
    'smoking_status_smokes'
]

@app.route("/")
def home():
    return "API Stroke Prediction is running"

@app.route("/predict", methods=["POST"])
def predict():
    try:
        data = request.get_json()
        args = data.get("inputs", [])

        if len(args) != 15:
            return jsonify({"error": "15 inputs required"}), 400

        df = pd.DataFrame([args], columns=feature_names)

        prob = model.predict_proba(df)[0][1]
        pred = model.predict(df)[0]

        increasing_risk_factors = []

        if SHAP_AVAILABLE:
            try:
                explainer = shap.TreeExplainer(model)
                shap_values = explainer.shap_values(df)

                shap_array = np.array(shap_values)

                if len(shap_array.shape) == 3:
                    shap_vals = shap_array[0, :, 1]
                elif isinstance(shap_values, list):
                    shap_vals = shap_values[1][0]
                else:
                    shap_vals = shap_values[0]

                for i, val in enumerate(shap_vals):
                    if val > 0.001 and feature_names[i] != 'age':
                        increasing_risk_factors.append({
                            "name": feature_names[i],
                            "value": args[i],
                            "shap_value": float(val)
                        })

            except:
                pass

        return jsonify({
            "probability": float(prob),
            "prediction": int(pred),
            "increasing_risk_factors": increasing_risk_factors
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 500


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=10000)
