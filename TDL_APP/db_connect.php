<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'todo_app';


$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


$sql = "CREATE TABLE IF NOT EXISTS tasks (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_name VARCHAR(255) NOT NULL,
    status ENUM('Belum Selesai', 'Selesai') DEFAULT 'Belum Selesai',
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    deadline DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}
?>