<?php
require 'config.php';
require_role('admin');
$user = current_user();
?>
<h2>Hai, <?=htmlspecialchars($user['name'])?> (Admin)</h2>
<a href="logout.php">Log Keluar</a> | 
<a href="admin_manage_users.php">Urus Pengguna</a>
<hr>
