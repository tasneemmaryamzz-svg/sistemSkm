<?php
require 'config.php';
require_role('pensyarah');
$user=current_user();
$lecturer_id=$user['id'];

$stmt = $pdo->prepare("SELECT * FROM courses WHERE lecturer_id=?");
$stmt->execute([$lecturer_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$course_id = $_GET['course_id'] ?? null;
$msg='';

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['course_id'])){
    $course_id=$_POST['course_id'];
    // verify ownership
    $stmt=$pdo->prepare("SELECT * FROM courses WHERE id=? AND lecturer_id=?");
    $stmt->execute([$course_id,$lecturer_id]);
    if(!$stmt->fetch()) die("Tidak dibenarkan");

    foreach($_POST['marks'] as $student_id=>$mark){
        $m=$mark===''?null:floatval($mark);
        $stmt=$pdo->prepare("SELECT id FROM marks WHERE student_id=? AND course_id=?");
        $stmt->execute([$student_id,$course_id]);
        if($stmt->fetch()){
            $u=$pdo->prepare("UPDATE marks SET mark=?, updated_by=?, updated_at=NOW() WHERE student_id=? AND course_id=?");
            $u->execute([$m,$lecturer_id,$student_id,$course_id]);
        } else {
            $i=$pdo->prepare("INSERT INTO marks(student_id,course_id,mark,updated_by,updated_at) VALUES(?,?,?,?,NOW())");
            $i->execute([$student_id,$course_id,$m,$lecturer_id]);
        }
    }
    $msg="Markah berjaya disimpan.";
}

$students=[];
if($course_id){
    $stmt=$pdo->prepare("SELECT u.id,u.uid,u.name,m.mark FROM users u 
        JOIN enrollment e ON e.student_id=u.id
        LEFT JOIN marks m ON m.student_id=u.id AND m.course_id=?
        WHERE e.course_id=? ORDER BY u.name");
    $stmt->execute([$course_id,$course_id]);
    $students=$stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<h2>Masukkan Markah</h2>
<a href="dashboard_lecturer.php">Dashboard Pensyarah</a> | <a href="logout.php">Log Keluar</a>
<hr>
<h3>Pilih Kursus</h3>
<ul>
<?php foreach($courses as $c): ?>
<li><a href="?course_id=<?=$c['id']?>"><?=htmlspecialchars($c['title'])?></a></li>
<?php endforeach; ?>
</ul>

<?php if($course_id): ?>
<h3>Masukkan Markah untuk Kursus ID <?=$course_id?></h3>
<?php if($msg) echo "<p class='ok'>$msg</p>"; ?>
<form method="post">
<input type="hidden" name="course_id" value="<?=$course_id?>">
<table>
<tr><th>No KP</th><th>Nama</th><th>Markah</th></tr>
<?php foreach($students as $s): ?>
<tr>
<td><?=htmlspecialchars($s['uid'])?></td>
<td><?=htmlspecialchars($s['name'])?></td>
<td><input name="marks[<?=$s['id']?>]" value="<?=htmlspecialchars($s['mark'])?>"></td>
</tr>
<?php endforeach; ?>
</table>
<button type="submit">Simpan Semua</button>
</form>
<?php endif; ?>
