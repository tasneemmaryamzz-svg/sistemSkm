<?php
require 'config.php';
require_role('pelajar');
$user = current_user();

// ambil semua kursus & markah pelajar ni
$stmt = $pdo->prepare("
    SELECT c.title, m.mark, m.status 
    FROM courses c
    JOIN enrollment e ON e.course_id = c.id
    LEFT JOIN marks m ON m.student_id = e.student_id AND m.course_id = c.id
    WHERE e.student_id = ?
    ORDER BY c.title
");
$stmt->execute([$user['id']]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset='utf-8'>
<title>Dashboard Pelajar</title>
<link rel='stylesheet' href='assets/style.css'>
</head>
<body>
<div class='card'>
<h2>Hai, <?=htmlspecialchars($user['name'])?> (Pelajar)</h2>
<a href="logout.php">Log Keluar</a>
<hr>

<h3>Keputusan SKM Anda</h3>
<table>
<tr><th>Kursus</th><th>Markah</th><th>Status</th></tr>
<?php foreach($courses as $c): ?>
<tr>
<td><?=htmlspecialchars($c['title'])?></td>
<td><?= $c['mark'] ?? '-' ?></td>
<td><?= htmlspecialchars($c['status'] ?? 'Sedang Diproses') ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
