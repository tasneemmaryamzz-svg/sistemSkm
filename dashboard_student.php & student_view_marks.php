<?php
require 'config.php';
require_role('pelajar');
$user=current_user();

$stmt=$pdo->prepare("SELECT c.title,m.mark,m.status
FROM courses c
JOIN enrollment e ON e.course_id=c.id
LEFT JOIN marks m ON m.student_id=? AND m.course_id=c.id
WHERE e.student_id=?");
$stmt->execute([$user['id'],$user['id']]);
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Keputusan SKM - <?=htmlspecialchars($user['name'])?></h2>
<a href="logout.php">Log Keluar</a>
<table>
<tr><th>Kursus</th><th>Markah</th><th>Status</th></tr>
<?php foreach($rows as $r): ?>
<tr>
<td><?=htmlspecialchars($r['title'])?></td>
<td><?= $r['mark'] ?? '-' ?></td>
<td><?=htmlspecialchars($r['status'] ?? 'processing')?></td>
</tr>
<?php endforeach; ?>
</table>
