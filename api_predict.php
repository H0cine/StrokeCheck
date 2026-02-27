<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $age = $_POST["age"];
    $glucose = $_POST["glucose"];
    $bmi = $_POST["bmi"];

    $gender_Male = ($_POST["gender"] == "1") ? 1 : 0;
    $Residence_type_Urban = ($_POST["residence"] == "1") ? 1 : 0;
    $hypertension_1 = $_POST["hypertension"];
    $heart_disease_1 = $_POST["disease"];
    $ever_married_Yes = $_POST["married"];

    $work = $_POST["work"];
    $wt_never    = ($work == "0") ? 1 : 0;
    $wt_private  = ($work == "1") ? 1 : 0;
    $wt_self     = ($work == "2") ? 1 : 0;
    $wt_children = ($work == "3") ? 1 : 0;

    $sm = $_POST["smoking"];
    $sm_former = ($sm == "1") ? 1 : 0;
    $sm_never  = ($sm == "2") ? 1 : 0;
    $sm_smokes = ($sm == "3") ? 1 : 0;

    $args = [
        $age,
        $glucose,
        $bmi,
        $gender_Male,
        $hypertension_1,
        $heart_disease_1,
        $ever_married_Yes,
        $wt_never,
        $wt_private,
        $wt_self,
        $wt_children,
        $Residence_type_Urban,
        $sm_former,
        $sm_never,
        $sm_smokes
    ];

    // Escape safely
    $escaped = implode(" ", array_map("escapeshellarg", $args));

    $python_path = "/home/hocine/Documents/brain_stroke_web/venv/bin/python";
    $script_path = "/home/hocine/Documents/brain_stroke_web/test.py";
    $cmd = "$python_path $script_path $escaped";

    $output = shell_exec($cmd);
    
    // Return the JSON output directly from Python
    echo $output;
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
