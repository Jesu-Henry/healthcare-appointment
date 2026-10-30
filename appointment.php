<?php
require_once 'db_config.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['full_phone'] ?? '');
    $appointment_date = $_POST['date'] ?? '';
    $department = $_POST['department'] ?? '';
    $doctor = $_POST['doctor'] ?? '';
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$phone || !$appointment_date || !$department || !$doctor) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled!']);
        exit;
    }

    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $appointment_date);
    if (!$dt) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid date format']);
        exit;
    }
    $formatted_date = $dt->format('Y-m-d H:i:s');

    try {
     
        $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            appointment_date DATETIME NOT NULL,
            department VARCHAR(50) NOT NULL,
            doctor VARCHAR(100) NOT NULL,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

      
        $stmt = $pdo->prepare("INSERT INTO appointments 
            (patient_name, email, phone, appointment_date, department, doctor, message) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([$name, $email, $phone, $formatted_date, $department, $doctor, $message]);

        echo json_encode(['status' => 'success', 'message' => 'Appointment booked successfully!']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>