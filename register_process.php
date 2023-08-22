<?php
include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];


    // パスワードと確認用パスワードが一致するかチェック
    if ($password !== $password_confirm) {
        // 一致しない場合、エラーメッセージをセッションに保存してregister.phpにリダイレクト
        session_start();
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit;
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: login.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>
