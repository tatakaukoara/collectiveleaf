
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

session_start();
include 'dbconnect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$book_id = $_GET['book_id'];
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'date_desc'; // デフォルトは新着順

// ブック情報を取得
$sql_book = "SELECT title, author FROM books WHERE id = :book_id";
$stmt_book = $pdo->prepare($sql_book);
$stmt_book->bindParam(":book_id", $book_id);
$stmt_book->execute();
$book = $stmt_book->fetch();

$title = $book["title"];
$author = $book["author"];

// メモを追加する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = $_POST['note'];
    $page_number = $_POST['page_number'] ?? null;
    
    $current_date = date('Y-m-d H:i:s');
    $sql_insert = "INSERT INTO memos (note, page_number, book_id, user_id, date) VALUES (:note, :page_number, :book_id, :user_id, :date)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->bindParam(":note", $note);
    $stmt_insert->bindParam(":page_number", $page_number, PDO::PARAM_INT);
    $stmt_insert->bindParam(":book_id", $book_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(":date", $current_date);
    $stmt_insert->execute();
}

$show_all_memos = isset($_GET['show_all']) && $_GET['show_all'] == '1';

// メモを取得
$sql_order_by = $order_by == 'page_asc' ? "ORDER BY IF(ISNULL(page_number),0,1) DESC, page_number ASC, date DESC" : "ORDER BY date DESC";
if ($show_all_memos) {
    $sql_memo = "SELECT memos.*, users.username FROM memos JOIN users ON memos.user_id = users.id WHERE book_id = :book_id $sql_order_by";
} else {
    $sql_memo = "SELECT * FROM memos WHERE book_id = :book_id AND user_id = :user_id $sql_order_by";
}
$stmt_memo = $pdo->prepare($sql_memo);
$stmt_memo->bindParam(":book_id", $book_id);
if (!$show_all_memos) {
    $stmt_memo->bindParam(":user_id", $user_id);
}
$stmt_memo->execute();
$memos = $stmt_memo->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notes - collectiveleaf</title>
    <script>
        function toggleAddMemoForm() {
            const form = document.getElementById('addMemoForm');
            form.style.display = form.style.display === 'none' ? 'flex' : 'none';
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f7f9fc;
            color: #333;
        }

        h2, h3 {
            color: #00796B;
            padding: 10px 0;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        input[type="text"], input[type="password"], textarea, input[type="number"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px;
        }

        input[type="submit"], button {
            padding: 10px 15px;
            background-color: #00796B;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        a, button {
            color: #00796B;
            text-decoration: none;
            margin-right: 10px;
        }
        
        button {
            padding: 10px 15px;
            background-color: #00574B; /* 濃い緑色に変更 */
            color: white; /* テキストの色を白に変更 */
             border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s; /* ホバー効果の追加 */
        }

        button:hover {
            background-color: #00796B; /* ホバー時の色 */
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background-color: #fff;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        #addMemoForm {
            display: none;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    
<h1><?php echo htmlspecialchars($title); ?></h1>
<p>Author: <?php echo htmlspecialchars($author); ?></p>

<h2>Notes</h2>

<button onclick="toggleAddMemoForm()">Add Note</button>

<div id="addMemoForm">
    <form action="" method="post">
        <label for="page_number">Page Number (Optional):</label>
        <input type="number" name="page_number" id="page_number">
        <label for="note">Note:</label>
        <textarea name="note" id="note" rows="4" cols="50"></textarea>
        <input type="submit" value="Add Note">
    </form>
</div>

<h3>Sort By:</h3>
<a href="view_memo.php?book_id=<?= $book_id ?>&order_by=page_asc">Page Number</a> | 
<a href="view_memo.php?book_id=<?= $book_id ?>&order_by=date_desc">Newest</a>

<ul>
<?php
foreach ($memos as $memo) {
    echo "<li>";
    if ($show_all_memos) {
        echo "<strong>" . htmlspecialchars($memo["username"]) . ":</strong> ";
    }
    if ($memo["page_number"]) {
        echo "Page: " . htmlspecialchars($memo["page_number"]) . " - ";
    }
    echo htmlspecialchars($memo["note"]) . " <small>(" . htmlspecialchars($memo["date"]) . ")</small></li>";
}
?>
</ul>

<!-- Button to show notes from other users -->
<?php if (!$show_all_memos): ?>
    <a href="view_memo.php?book_id=<?= $book_id ?>&show_all=1">Show Notes from Others</a>
<?php else: ?>
    <a href="view_memo.php?book_id=<?= $book_id ?>">Show Only My Notes</a>
<?php endif; ?>

<a href="mypage.php">Return to My Page</a>

</body>
</html>
