<?php
include 'dbconnect.php';

$user_id = $_GET['user_id'];

$sql = "SELECT icon FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch();

header("Content-Type: image/png");
echo $user['icon'];
?>
