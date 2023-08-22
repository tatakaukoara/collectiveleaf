<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - collectiveleaf</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h2 {
            color: #00796B;
            border-bottom: 2px solid #00796B;
            padding-bottom: 10px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            padding: 10px 15px;
            background-color: #00796B;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        a {
            color: #00796B;
        }

    </style>
</head>
<body>
    <h2>Search Books</h2>

    <!-- Book Search Form -->
    <form action="search_books.php" method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title">
        <label for="author">Author:</label>
        <input type="text" id="author" name="author">
        <br>
        <input type="submit" value="Search">
    </form>

<?php 
include 'dbconnect.php'; 
session_start();

// ユーザーがログインしていない場合はログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
}

// マイ本棚に追加ボタンがクリックされた場合の処理
if (isset($_GET['add_to_shelf'])) {
    $book_id = $_GET['add_to_shelf'];
    $user_id = $_SESSION['user_id']; 

    // ユーザーのマイ本棚カラムを取得
    $sql = "SELECT my_bookshelf FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_bookshelf = json_decode($row['my_bookshelf'], true);
    if (!is_array($current_bookshelf)) {
        $current_bookshelf = [];
    }
    
    // 書籍IDが既にマイ本棚にない場合だけ追加
    if (!in_array($book_id, $current_bookshelf)) {
        $current_bookshelf[] = $book_id;

        $updated_bookshelf = json_encode($current_bookshelf);

        // 更新されたマイ本棚カラムをユーザーテーブルに保存
        $update_sql = "UPDATE users SET my_bookshelf = :updated_bookshelf WHERE id = :user_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->bindParam(':updated_bookshelf', $updated_bookshelf, PDO::PARAM_STR);
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $update_stmt->execute();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 両方のフィールドが空白の場合は処理をスキップ
    $title_term = trim($_POST['title']);
    $author_term = trim($_POST['author']);

    if ($title_term === '' && $author_term === '') {
        header("Location: search_books.php");
        exit;
    }

    $sql = "SELECT id, title, author FROM books WHERE ";
    $params = [];

    // タイトルフィールドが空でない場合
    if ($title_term !== '') {
        $like_title_term = "%" . $title_term . "%";
        $sql .= "title LIKE :title_term ";
        $params[':title_term'] = $like_title_term;

        if ($author_term !== '') {
            $sql .= "OR ";
        }
    }

    // 著者フィールドが空でない場合
    if ($author_term !== '') {
        $like_author_term = "%" . $author_term . "%";
        $sql .= "author LIKE :author_term ";
        $params[':author_term'] = $like_author_term;
    }

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();

    // 検索結果の表示
    echo "<h3>Search Results:</h3>";
    while ($row = $stmt->fetch()) {
        echo "<strong>Book:</strong> " . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . "<br>";
        echo "<strong>Author:</strong> " . htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8') . "<br>";
        echo "<a href='search_books.php?add_to_shelf=" . $row['id'] . "'>Add to MyBookShelf</a><hr>";
    }
}

?>

    
    <!-- 新規書籍データ追加ページへのリンク -->
   <p><a href="add_new_books.php">Add New Book  </a></p>※※If that book isn't here, push AddNew Book.
    <p><a href="mypage.php">Back to My Page</a></p>
    
</body>
</html>
