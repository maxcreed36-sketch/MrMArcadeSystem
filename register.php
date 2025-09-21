<?php
// login.php or register.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents('php://input'), true);

$hostname = 'sql106.infinityfree.com';
$username = 'if0_39983762';
$password = 'MaxCreed36';
$database = 'if0_39983762_users';

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($data['action'] == 'login') {
    $username = $conn->real_escape_string($data['username']);
    $password = $conn->real_escape_string($data['password']);
    
    $sql = "SELECT * FROM users WHERE UserName = '$username' AND Pascode = '$password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} elseif ($data['action'] == 'register') {
    $username = $conn->real_escape_string($data['username']);
    $password = $conn->real_escape_string($data['password']);
    
    // Check if user already exists
    $checkSql = "SELECT * FROM users WHERE UserName = '$username'";
    $checkResult = $conn->query($checkSql);
    
    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
    } else {
        $insertSql = "INSERT INTO users (UserName, Pascode, Score, History) VALUES ('$username', '$password', 0, '')";
        if ($conn->query($insertSql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
    }
}

$conn->close();
?>