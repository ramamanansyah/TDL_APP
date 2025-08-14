CREATE DATABASE IF NOT EXISTS todo_app;
USE todo_app;

CREATE TABLE IF NOT EXISTS tasks (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_name VARCHAR(255) NOT NULL,
    status ENUM('Belum Selesai', 'Selesai') DEFAULT 'Belum Selesai',
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    deadline DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO tasks (task_name, status, priority, deadline) VALUES
('Mengerjakan tugas PHP', 'Belum Selesai', 'High', NOW() + INTERVAL 1 DAY),
('Memperbaiki bug CSS', 'Selesai', 'Medium', NOW() - INTERVAL 1 DAY),
('Buat presentasi proyek', 'Belum Selesai', 'Low', NULL),
('Kirim laporan bulanan', 'Belum Selesai', 'High', NOW() + INTERVAL 3 HOUR);

