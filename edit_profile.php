<?php
// エラー出力の有効化
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'dbconnect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$user = $stmt->fetch();
$message = '';


// プロフィール更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    
    if (isset($_FILES["icon"]) && $_FILES["icon"]["error"] == UPLOAD_ERR_OK) {
        $icon_data = file_get_contents($_FILES["icon"]["tmp_name"]);
    }

    $sql = "UPDATE users SET username = :username, icon = :icon WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $new_username);
    $stmt->bindParam(':icon', $icon_data, PDO::PARAM_LOB);
    $stmt->bindParam(':user_id', $user_id);
    
    if ($stmt->execute()) {
        header("Location: mypage.php"); 
        exit;  // リダイレクト後、このスクリプトの実行を止めるためにexitを追加
    } else {
        $message = 'データベースの更新に失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - collectiveleaf</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }

        header {
            background-color: #00796B;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }

        header h1 {
            margin: 0;
            color: white;
            font-size: 24px;
        }

        h2 {
            color: #00796B;
        }

        a {
            color: #00796B;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        label {
            display: block;
            margin-top: 20px;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            margin-top: 20px;
        }

    </style>
</head>
<body>

<header>
    <h1>collectiveleaf</h1>
</header>

<h2>プロフィール編集</h2>

<?php if ($message): ?>
    <p style="color: red;"><?= $message ?></p>
<?php endif; ?>

<form action="edit_profile.php" method="post" enctype="multipart/form-data">
    <label for="username">名前:</label>
    <input type="text" name="username" id="username" value="<?= $user["username"] ?>">

    <label for="icon">アイコン:</label>
    <input type="file" name="icon" id="icon">

    <input type="submit" value="更新">
</form>

<a href="mypage.php">マイページに戻る</a>

</body>
</html>