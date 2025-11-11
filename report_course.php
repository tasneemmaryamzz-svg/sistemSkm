<?php
require 'config.php';
require_role('pensyarah');
$user=current_user();
$course_id=$_GET['course_id']??null;

// verify ownership
$stmt=$pdo->prepare("SELECT * FROM courses WHERE id=? AND lecturer_id=?");
$stmt->execute([$course_id,$user['id']]);
$course=$stmt->fetch();
if(!$course) die("Tidak dibenarkan");

$q=$pdo->prepare("SELECT COUNT(m.mark) cnt, AVG(m.mark) avgm, MIN(m.mark) minm, MAX(m.mark) maxm
FROM marks m WHERE m.course_id=?");
$q->execute([$course_id]);
$stat=$q->fetch(PDO::FETCH_ASSOC);

$dist_q=$pdo->prepare("SELECT
SUM(m.mark>=80) AS gA,
SUM(m.mark>=60 AND m.mark<80) AS gB,
SUM(m.mark>=40 AND m.mark<60) AS gC,
SUM(m.mark<40) AS gD
FROM marks m WHERE m.course_id=?");
$dist_q->execute([$course_id]);
$dist=$dist_q->fetch(PDO::FETCH_ASSOC);

?>
<h2>Laporan Kursus: <?=htmlspecialchars($course['title'])?></h2>
<a href="dashboard_lecturer.php">Dashboard Pensyarah</a> | <a href="logout.php">Log Keluar</a>
<p>Jumlah pelajar dgn markah: <?=$stat['cnt']?></p>
<p>Purata: <?=round($stat['avgm'],2)?> Min: <?=$stat['minm']?> Max: <?=$stat['maxm']?></p>
<p>Pengagihan gred: A(>=80): <?=$dist['gA']?>, B(60-79): <?=$dist['gB']?>, C(40-59): <?=$dist['gC']?>, D(<40): <?=$dist['gD']?></p>
