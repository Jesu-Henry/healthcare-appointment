<?php
// patient_form.php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'patients_form_db';
$username = 'root@localhost';
$password = 'lilscare';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = trim($_POST['patient_name']);
    $patient_id = trim($_POST['patient_id']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? true : false;
    
    // Validate input
    if (empty($patient_name) || empty($patient_id) || empty($password)) {
        $error = "All fields are required";
    } else {
        // Check if patient exists and verify password
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ? AND patient_name = ? AND is_active = 1");
        $stmt->execute([$patient_id, $patient_name]);
        $patient = $stmt->fetch();
        
        if ($patient && password_verify($password, $patient['password'])) {
            // Login successful
            $_SESSION['patient_id'] = $patient['patient_id'];
            $_SESSION['patient_name'] = $patient['patient_name'];
            $_SESSION['patient_email'] = $patient['email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Handle remember me functionality
            if ($remember_me) {
                // Set cookie for 30 days
                $cookie_token = bin2hex(random_bytes(32));
                $cookie_expires = time() + (30 * 24 * 60 * 60); // 30 days
                
                // Store token in database
                $stmt = $pdo->prepare("UPDATE patients SET remember_token = ? WHERE patient_id = ?");
                $stmt->execute([$cookie_token, $patient_id]);
                
                setcookie('remember_token', $cookie_token, $cookie_expires, '/');
            }
            
            // Update last login
            $update_stmt = $pdo->prepare("UPDATE patients SET last_login = NOW(), login_count = login_count + 1 WHERE patient_id = ?");
            $update_stmt->execute([$patient_id]);
            
            // Return JSON response for AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Login successful']);
                exit();
            }
            
            // Redirect to dashboard
            header("Location: patient_dashboard.php");
            exit();
        } else {
            $error = "Invalid patient credentials";
            
            // Return JSON response for AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error]);
                exit();
            }
        }
    }
}

// Check for remember me cookie
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE remember_token = ? AND is_active = 1");
    $stmt->execute([$token]);
    $patient = $stmt->fetch();
    
    if ($patient) {
        $_SESSION['patient_id'] = $patient['patient_id'];
        $_SESSION['patient_name'] = $patient['patient_name'];
        $_SESSION['patient_email'] = $patient['email'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        header("Location: patient_dashboard.php");
        exit();
    }
}
?>