<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Log all POST data
    error_log("POST Data: " . print_r($_POST, true));
    
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $date_of_birth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $patient_type = isset($_POST['patient_type']) ? $_POST['patient_type'] : '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : ''; 
    
    // Show alert with values to be inserted
    echo "<script>alert('Values to insert:\nFirst Name: $first_name\nLast Name: $last_name\nEmail: $email\nPhone: $phone\nDate of Birth: $date_of_birth\nGender: $gender\nPatient Type: $patient_type');</script>";
    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($date_of_birth) || empty($gender) || empty($patient_type) || empty($_POST['password'])) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, date_of_birth, gender, patient_type, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$first_name, $last_name, $email, $phone, $date_of_birth, $gender, $patient_type, $password]);
        
        if ($result) {
            echo "<script>alert('Sign up successful!'); window.location.href='home.html';</script>";
        } else {
            echo "<script>alert('Failed to insert data'); window.history.back();</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request method'); window.location.href='index.html';</script>";
}
?>