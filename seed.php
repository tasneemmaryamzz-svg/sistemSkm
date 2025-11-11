<?php
require 'config.php';

echo "<h3>Menjana jadual dan data...</h3>";

// 1️⃣ Hapus semua jadual lama
$pdo->exec("DROP TABLE IF EXISTS marks, enrollment, courses, users");

// 2️⃣ Cipta jadual baru
$pdo->exec("
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  uid VARCHAR(20) UNIQUE,
  name VARCHAR(100),
  role ENUM('admin','pensyarah','pelajar'),
  password_hash TEXT
)");

$pdo->exec("
CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(10),
  title VARCHAR(100),
  lecturer_id INT,
  FOREIGN KEY (lecturer_id) REFERENCES users(id)
)");

$pdo->exec("
CREATE TABLE enrollment (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  course_id INT,
  FOREIGN KEY (student_id) REFERENCES users(id),
  FOREIGN KEY (course_id) REFERENCES courses(id)
)");

$pdo->exec("
CREATE TABLE marks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  course_id INT,
  mark DECIMAL(5,2),
  status VARCHAR(20) DEFAULT 'processing',
  updated_by INT,
  updated_at DATETIME,
  FOREIGN KEY (student_id) REFERENCES users(id),
  FOREIGN KEY (course_id) REFERENCES courses(id)
)");
echo "<p>✅ Jadual berjaya dibuat.</p>";

// 3️⃣ Masukkan data pengguna
$addUser = $pdo->prepare("INSERT INTO users(uid,name,role,password_hash) VALUES (?,?,?,?)");

$addUser->execute(['admin001','Admin SKM','admin', password_hash('admin123', PASSWORD_DEFAULT)]);

// Tambah 7 pensyarah
for($i=1;$i<=7;$i++){
    $addUser->execute(["90010$i","Pensyarah $i",'pensyarah', password_hash('pensyarah123', PASSWORD_DEFAULT)]);
}
echo "<p>✅ Pensyarah dimasukkan.</p>";

// Tambah 20 pelajar
for($i=1;$i<=20;$i++){
    $addUser->execute(["99010$i","Pelajar $i",'pelajar', password_hash('pelajar123', PASSWORD_DEFAULT)]);
}
echo "<p>✅ Pelajar dimasukkan.</p>";

// 4️⃣ Cipta 7 kursus dan link dengan pensyarah
$addCourse = $pdo->prepare("INSERT INTO courses(code,title,lecturer_id) VALUES (?,?,?)");
for($i=1;$i<=7;$i++){
    $addCourse->execute(["KURSUS$i","Kursus SKM $i",$i+1]); // pensyarah id dari 2 hingga 8
}
echo "<p>✅ Kursus berjaya ditambah.</p>";

// 5️⃣ Daftarkan setiap pelajar dalam semua kursus
$students = $pdo->query("SELECT id FROM users WHERE role='pelajar'")->fetchAll(PDO::FETCH_COLUMN);
$courses = $pdo->query("SELECT id FROM courses")->fetchAll(PDO::FETCH_COLUMN);
$enroll = $pdo->prepare("INSERT INTO enrollment(student_id,course_id) VALUES (?,?)");

foreach($students as $sid){
    foreach($courses as $cid){
        $enroll->execute([$sid,$cid]);
    }
}
echo "<p>✅ Semua pelajar didaftarkan dalam 7 kursus.</p>";

echo "<h3>🎉 Data awal berjaya disetup!</h3>";
