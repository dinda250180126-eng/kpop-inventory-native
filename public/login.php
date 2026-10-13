<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/escape.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Email atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login - K-Pop Inventory Native</title></head>
<body style="font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 90vh; background: #f8fafc;">
    <form method="POST" style="background: white; padding: 24px; border: 1px solid #cbd5e1; border-radius: 8px; width: 320px;">
        <h3 style="margin-top:0;">Login PHP Native</h3>
        <?php if ($error): ?><p style="color: red; font-size: 14px;"><?= e($error) ?></p><?php endif; ?>
        <div style="margin-bottom: 12px;">
            <label>Email:</label><br>
            <input type="email" name="email" required style="width: 100%; padding: 6px; box-sizing: border-box;">
        </div>
        <div style="margin-bottom: 16px;">
            <label>Password:</label><br>
            <input type="password" name="password" required style="width: 100%; padding: 6px; box-sizing: border-box;">
        </div>
        <button type="submit" style="width: 100%; padding: 8px; background: #0284c7; color: white; border: none; border-radius: 4px; cursor: pointer;">Masuk</button>
    </form>
</body>
</html>