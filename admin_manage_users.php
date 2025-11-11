<?php
require 'config.php';
require_role('admin');
$msg = '';

if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['add_user'])){
    $uid = $_POST['uid']; $name = $_POST['name']; $role = $_POST['role'];
    $default = $role==='admin'?'admin123':($role==='pensyarah'?'pensyarah123':'pelajar123');
    $h = password_hash($default,PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users(uid,name,role,password_hash) VALUES(?,?,?,?)");
    $stmt->execute([$uid,$name,$role,$h]);
    $msg="Pengguna berjaya ditambah.";
}

$users = $pdo->query("SELECT * FROM users ORDER BY role,name")->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Urus Pengguna</h2>
<a href="dashboard_admin.php">Dashboard Admin</a><br><br>
<?php if($msg) echo "<p class='ok'>$msg</p>"; ?>

<form method="post">
<h3>Tambah Pengguna Baru</h3>
UID: <input name="uid" required>
Nama: <input name="name" required>
Role: 
<select name="role">
<option value="admin">Admin</option>
<option value="pensyarah">Pensyarah</option>
<option value="pelajar">Pelajar</option>
</select>
<button name="add_user">Tambah</button>
</form>

<h3>Senarai Pengguna</h3>
<table>
<tr><th>UID</th><th>Nama</th><th>Role</th></tr>
<?php foreach($users as $u): ?>
<tr>
<td><?=htmlspecialchars($u['uid'])?></td>
<td><?=htmlspecialchars($u['name'])?></td>
<td><?=htmlspecialchars($u['role'])?></td>
</tr>
<?php endforeach; ?>
</table>
