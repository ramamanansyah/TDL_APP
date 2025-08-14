<?php
require_once 'db_connect.php';

// Menambah tugas dengan user_id
function addTask($user_id, $task_name, $priority = 'Medium', $deadline = null) {
    global $conn;
    
    if (!empty($task_name)) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name, priority, deadline) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $task_name, $priority, $deadline);
        return $stmt->execute();
    }
    return false;
}

// Mengambil semua tugas milik user tertentu
function getAllTasks($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT *, 
        CASE 
            WHEN deadline IS NULL THEN 0
            WHEN deadline < NOW() THEN 2 
            ELSE 1 
        END AS deadline_status 
        FROM tasks 
        WHERE user_id = ?
        ORDER BY FIELD(priority, 'High', 'Medium', 'Low'), deadline_status DESC, deadline ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Menghapus tugas milik user tertentu
function deleteTask($task_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    return $stmt->execute();
}

// Toggle status tugas milik user tertentu
function toggleTaskStatus($task_id, $current_status, $user_id) {
    global $conn;
    
    $new_status = ($current_status === 'Belum Selesai') ? 'Selesai' : 'Belum Selesai';
    
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $new_status, $task_id, $user_id);
    return $stmt->execute();
}

// Edit tugas milik user tertentu
function editTask($task_id, $task_name, $priority, $deadline, $user_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, priority = ?, deadline = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $task_name, $priority, $deadline, $task_id, $user_id);
    return $stmt->execute();
}

// Menghitung tugas pending milik user tertentu
function countPendingTasks($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM tasks WHERE status = 'Belum Selesai' AND user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Menghapus semua tugas milik user tertentu
function clearAllTasks($user_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM tasks WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}
?>