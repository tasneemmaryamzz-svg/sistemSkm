<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE uid=?");
    $stmt->execute([$uid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id'=>$user['id'],
            'uid'=>$user['uid'],
            'name'=>$user['name'],
            'role'=>$user['role'],
        ];
        // redirect
        if($user['role']=='admin') header('Location: dashboard_admin.php');
        elseif($user['role']=='pensyarah') header('Location: dashboard_lecturer.php');
        else header('Location: dashboard_student.php');
        exit;
    } else {
        $error = "ID atau Password salah.";
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login SKM</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<div class="card">
<h2>Login SKM</h2>
<?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>
<form method="post">
<label>ID (No KP)<br><input name="uid" required></label><br><br>
<label>Password<br><input type="password" name="password" required></label><br><br>
<button type="submit">Log Masuk</button>
</form>
<p>Default Password: <br>Admin: admin123<br>Pensyarah: pensyarah123<br>Pelajar: pelajar123</p>
</div>
</body>
</html>
