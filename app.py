from flask import Flask, request, jsonify
import joblib

# ================================
# LOAD MODEL + VECTORIZER (ONCE)
# ================================
model = joblib.load("model.joblib")
vectorizer = joblib.load("vectorizer.joblib")

app = Flask(__name__)

# ================================
# OPTIONAL: CLEANING FUNCTION
# (match your notebook if you had one)
# ================================
def preprocess_text(text):
    # Example basic cleaning (adjust if needed)
    return text.strip()

# ================================
# HOME ROUTE (TESTING)
# ================================
@app.route('/')
def home():
    return "API is running!"

# ================================
# PREDICT ROUTE
# ================================
@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()

        if not data or 'review' not in data:
            return jsonify({"error": "Missing 'review' field"}), 400

        review = preprocess_text(data['review'])

        if review == "":
            return jsonify({"error": "Empty input"}), 400

        # Transform using SAME vectorizer
        review_vec = vectorizer.transform([review])

        # Predict
        prediction = model.predict(review_vec)[0]

        return jsonify({
            "review": review,
            "prediction": str(prediction)
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 500

# ================================
# RUN SERVER
# ================================
if __name__ == '__main__':
    app.run(debug=True)
    