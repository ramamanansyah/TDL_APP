<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'functions.php';

// Mendapatkan user_id dari session
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['add_task'])) {
        $task_name = trim($_POST['task_name']);
        $priority = $_POST['priority'] ?? 'Medium';
        $deadline = $_POST['deadline'] ?? null;
        addTask($user_id, $task_name, $priority, $deadline);
    } 
  
    elseif (isset($_POST['delete_task'])) {
        $task_id = $_POST['task_id'];
        deleteTask($task_id, $user_id);
    } 
    
    elseif (isset($_POST['toggle_status'])) {
        $task_id = $_POST['task_id'];
        $current_status = $_POST['current_status'];
        toggleTaskStatus($task_id, $current_status, $user_id);
    } 
    
    elseif (isset($_POST['edit_task'])) {
        $task_id = $_POST['task_id'];
        $task_name = trim($_POST['task_name']);
        $priority = $_POST['priority'];
        $deadline = $_POST['deadline'] ?? null;
        editTask($task_id, $task_name, $priority, $deadline, $user_id);
    } 
    
    elseif (isset($_POST['clear_all'])) {
        clearAllTasks($user_id);
    }
}

header("Location: index.php");
exit;
?>