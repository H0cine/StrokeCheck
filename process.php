<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Récupération des données du formulaire
    $age = $_POST["age"];
    $glucose = $_POST["glucose"];
    $bmi = $_POST["bmi"];
    $gender = $_POST["gender"];
    $residence = $_POST["residence"];
    $hypertension = $_POST["hypertension"];
    $disease = $_POST["disease"];
    $married = $_POST["married"];
    $work = $_POST["work"];
    $smoking = $_POST["smoking"];
    
    // Encodage PHP (identique à votre logique actuelle)
    $gender_Male          = ($gender == "1") ? 1 : 0;
    $Residence_type_Urban = ($residence == "1") ? 1 : 0;
    $wt_never             = ($work == "0") ? 1 : 0;
    $wt_private           = ($work == "1") ? 1 : 0;
    $wt_self              = ($work == "2") ? 1 : 0;
    $wt_children          = ($work == "3") ? 1 : 0;
    $sm_former            = ($smoking == "1") ? 1 : 0;
    $sm_never             = ($smoking == "2") ? 1 : 0;
    $sm_smokes            = ($smoking == "3") ? 1 : 0;

    $feature_array = [
        $age, $glucose, $bmi, $gender_Male, $hypertension, $disease, $married, 
        $wt_never, $wt_private, $wt_self, $wt_children, $Residence_type_Urban, 
        $sm_former, $sm_never, $sm_smokes
    ];
    
    $escaped_args = implode(" ", array_map("escapeshellarg", $feature_array));
    $python_path = "python3"; // Utilise le python du système (OK pour Railway)

    // --- ÉTAPE 1 : PRÉDICTION (test.py) ---
    $predict_cmd = "$python_path test.py $escaped_args 2>&1";
    $prediction_json_raw = shell_exec($predict_cmd);
    
    // On décode pour vérifier que ça a marché et pour extraire les features pour l'IA
    $prediction_data = json_decode($prediction_json_raw, true);

    $ai_text = "Désolé, l'IA n'a pas pu générer de conseils car la prédiction a échoué.";

    if ($prediction_data && isset($prediction_data['increasing_risk_factors'])) {
        // --- ÉTAPE 2 : CONSEILS IA (AI_advice.py) ---
        // On envoie le JSON des features qui augmentent le risque (sauf l'âge) à AI_advice.py
        $features_for_ai = json_encode($prediction_data['increasing_risk_factors']);
        $escaped_json = escapeshellarg($features_for_ai);
        
        $advice_cmd = "$python_path AI_advice.py $escaped_json 2>&1";
        $ai_text = shell_exec($advice_cmd);
    }

    if (!$ai_text) {
        $ai_text = "❌ Aucune réponse de l'IA conseils.";
    }
    
    // --- ÉTAPE 3 : REDIRECTION ---
    header("Location: index.php?prediction=" . urlencode($prediction_json_raw) . "&response=" . urlencode($ai_text)); 
    exit();
}
?>
