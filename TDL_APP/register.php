<?php
session_start();
require_once 'db_connect.php';

$classes = ['10 RPL 1', '10 RPL 2', '10 TKJ 1', '10 TKJ 2', '10 BC 1', '10 BC 2',
            '11 RPL 1', '11 RPL 2', '11 TKJ 1', '11 TKJ 2', '11 BC 1', '11 BC 2',
            '12 RPL 1', '12 RPL 2', '12 TKJ 1', '12 TKJ 2', '12 BC 1', '12 BC 2'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $class = $_POST['class'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "Username sudah digunakan!";
    } else {
        
        $stmt = $conn->prepare("INSERT INTO users (fullname, class, username, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $class, $username, $password);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            header("Location: login.php");
            exit;
        } else {
            $error = "Terjadi kesalahan: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - To-Do List App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Registrasi Akun</h1>
        </div>
        
        <div class="card">
            <?php if (isset($error)): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>
            
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="fullname">Nama Lengkap</label>
                    <input type="text" name="fullname" placeholder="Contoh: Rama Manansyah" required>
                </div>
                
                <div class="form-group">
                    <label for="class">Kelas & Jurusan</label>
                    <select name="class" required>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c ?>"><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" placeholder="Buat username unik" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="Buat password" required>
                    <small class="password-hint">Format: NamaDepan + TahunLahir (Contoh: rama2007)</small>
                </div>
                
                <button type="submit" class="btn-register"><i class="fas fa-user-plus"></i> Daftar</button>
            </form>
            
            <div class="login-link">
                <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
            </div>
        </div>
    </div>
</body>
</html>