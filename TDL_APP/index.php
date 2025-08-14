<?php
session_start();
require_once 'db_connect.php';
require_once 'functions.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Mendapatkan user_id dari session
$user_id = $_SESSION['user_id'];

// Mengambil tugas milik user yang sedang login
$tasks = getAllTasks($user_id);
$pending_count = countPendingTasks($user_id);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To-Do List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tasks"></i> To-Do List App</h1>
            <div class="user-info">
                <span><?= $_SESSION['fullname'] ?> (<?= $_SESSION['class'] ?>)</span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="card">
            <form action="actions.php" method="POST" class="add-task">
                <input type="text" name="task_name" placeholder="Tambahkan tugas baru..." required>
                <select name="priority">
                    <option value="Low">Prioritas Rendah</option>
                    <option value="Medium" selected>Prioritas Medium</option>
                    <option value="High">Prioritas Tinggi</option>
                </select>
                <input type="datetime-local" name="deadline" class="deadline-input">
                <button type="submit" name="add_task"><i class="fas fa-plus"></i> Tambah</button>
            </form>
            
            <div class="task-stats">
                <p>Kamu memiliki <span><?= $pending_count ?></span> tugas yang belum selesai</p>
            </div>
            
            <?php if (empty($tasks)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Belum ada tugas</h3>
                    <p>Tambahkan tugas pertama Anda untuk memulai!</p>
                </div>
            <?php else: ?>
                <ul class="task-list">
                    <?php foreach ($tasks as $task): 
                        $deadline_passed = $task['deadline'] && strtotime($task['deadline']) < time();
                    ?>
                        <li class="task-item <?= $task['status'] === 'Selesai' ? 'completed' : '' ?>">
                            <div class="task-info">
                                <span class="priority-badge priority-<?= strtolower($task['priority']) ?>"><?= $task['priority'] ?></span>
                                <span class="task-name"><?= htmlspecialchars($task['task_name']) ?></span>
                                
                                <?php if ($task['deadline']): ?>
                                    <div class="deadline-info">
                                        <i class="fas fa-calendar"></i>
                                        <span class="deadline-date <?= $deadline_passed ? 'deadline-passed' : '' ?>">
                                            <?= date('d M Y H:i', strtotime($task['deadline'])) ?>
                                        </span>
                                        <?php if ($deadline_passed && $task['status'] !== 'Selesai'): ?>
                                            <span class="deadline-warning">Deadline sudah terlewat!</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="task-actions">
                                <form action="actions.php" method="POST" class="inline-form">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <input type="hidden" name="current_status" value="<?= $task['status'] ?>">
                                    <button type="submit" name="toggle_status" class="status-btn">
                                        <?= $task['status'] === 'Belum Selesai' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-undo"></i>' ?>
                                    </button>
                                </form>

                                <button class="edit-btn" onclick="openEditModal(<?= $task['id'] ?>, '<?= htmlspecialchars(addslashes($task['task_name'])) ?>', '<?= $task['priority'] ?>', '<?= $task['deadline'] ? date('Y-m-d\TH:i', strtotime($task['deadline'])) : '' ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="actions.php" method="POST" class="inline-form">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <button type="submit" name="delete_task" class="delete-btn" onclick="return confirm('Yakin ingin menghapus tugas ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="clear-all">
                    <form action="actions.php" method="POST">
                        <button type="submit" name="clear_all" onclick="return confirm('Yakin ingin menghapus SEMUA tugas?')"><i class="fas fa-broom"></i> Hapus Semua Tugas</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal Edit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Tugas</h2>
            <form id="editForm" action="actions.php" method="POST">
                <input type="hidden" name="task_id" id="editTaskId">
                <input type="text" name="task_name" id="editTaskName" required>
                <select name="priority" id="editPriority">
                    <option value="Low">Prioritas Rendah</option>
                    <option value="Medium">Prioritas Medium</option>
                    <option value="High">Prioritas Tinggi</option>
                </select>
                <input type="datetime-local" name="deadline" id="editDeadline" class="deadline-input">
                <button type="submit" name="edit_task"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </form>
        </div>
    </div>
    
    <script>
        function openEditModal(id, name, priority, deadline) {
            document.getElementById('editTaskId').value = id;
            document.getElementById('editTaskName').value = name;
            document.getElementById('editPriority').value = priority;
            document.getElementById('editDeadline').value = deadline;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>