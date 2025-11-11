<?php
require 'config.php';
require_role('pensyarah');
$user = current_user();
$lecturer_id = $user['id'];

// kursus milik pensyarah
$stmt = $pdo->prepare("SELECT * FROM courses WHERE lecturer_id=?");
$stmt->execute([$lecturer_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Hai, <?=htmlspecialchars($user['name'])?> (Pensyarah)</h2>
<a href="logout.php">Log Keluar</a>
<hr>
<h3>Kursus Anda</h3>
<ul>
<?php foreach($courses as $c): ?>
<li><a href="lecturer_enter_marks.php?course_id=<?=$c['id']?>"><?=htmlspecialchars($c['title'])?></a></li>
<?php endforeach; ?>
</ul>
