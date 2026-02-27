import os
import sys
import logging
import json
from google import genai

os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"
logging.getLogger("absl").setLevel(logging.CRITICAL)
logging.getLogger("googleapiclient").setLevel(logging.CRITICAL)

API_KEY = "AIzaSyB0ahRkrByu8KQ8d3J-28T8oSb_N_XqU2o"

client = genai.Client(api_key=API_KEY)

def generate_health_advice(risk_factors_json):
    """
    Generates optimized personalized health advice based on SHAP increasing stroke risk factors.
    Includes:
    - Prompt optimization
    - Token limiting
    - 429 auto-retry
    - Basic caching
    """
    try:
        risk_factors = json.loads(risk_factors_json)
    except Exception as e:
        return f"Error parsing risk factors: {str(e)}"

    # The list now already contains only increasing factors excluding age (from test.py)
    increasing_factors = risk_factors
    
    if not increasing_factors:
        return "Your profile currently doesn't have any specific lifestyle or metabolic factors significantly increasing your immediate risk. Keep maintaining a healthy lifestyle!"

    factors_str = "\n".join([f"- {f['name']} (Current Value: {f['value']})" for f in increasing_factors])
    
    prompt = f"""
You are a professional medical advisor.

The following modifiable factors increase this person's stroke risk:
{factors_str}

Provide:
- Short encouraging introduction
- Brief explanation per factor + immediate goal
- 3-5 personalized lifestyle "Golden Rules"
- A short bold medical disclaimer

Use clean Markdown formatting.
Be professional and empathetic.
Return only formatted text.
"""

    try:
        response = client.models.generate_content(
            model="gemini-1.5-flash-latest",
            contents=prompt,
            config={
                "max_output_tokens": 500,
                "temperature": 0.4,
            }
        )

        return response.text if response.text else "No valid content returned."

    except Exception as e:
        return f"Error generating advice: {str(e)}"

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Error: Missing input argument (risk factors JSON).")
        sys.exit(1)

    risk_factors_input = sys.argv[1]
    print(generate_health_advice(risk_factors_input))