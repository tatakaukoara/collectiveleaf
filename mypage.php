<?php

ini_set('display_errors', 0);


// セッションを開始
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

// マイ本棚に追加された書籍のIDを取得
$book_ids = json_decode($user["my_bookshelf"], true); // JSON文字列を配列に変換


// マイ本棚に追加された書籍の情報を取得
$books = [];
foreach ($book_ids as $book_id) {
    $book_sql = "SELECT * FROM books WHERE id = :book_id";
    $book_stmt = $pdo->prepare($book_sql);
    $book_stmt->bindParam(":book_id", $book_id);
    $book_stmt->execute();
    $book = $book_stmt->fetch();
    $books[] = $book;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Page - collectiveleaf</title>
    <style>
        /* Reset some default styles for a more consistent look across browsers */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #FAFAFA;
            color: #333;
            padding: 40px 15%;
            line-height: 1.5;
        }

        h2 {
            font-size: 2.5em;
            margin-bottom: 40px;
            text-align: center;
            color: #00796B; /* Deep teal color for emphasis */
            position: relative;
        }

        h2::after { /* Underline effect that doesn't span the full width */
            content: '';
            width: 100px;
            height: 3px;
            background-color: #00796B;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        h3 {
            margin-top: 40px;
            margin-bottom: 20px;
            font-weight: 400;
            font-size: 1.5em;
            border-bottom: 1px solid #DDDDDD;
            padding-bottom: 10px;
        }

        a {
            color: #00796B;
            text-decoration: none;
            padding: 8px 15px;
            background-color: #E0F2F1;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: inline-block;
            margin-right: 10px; /* Small margin to separate inline links */
        }

        a:hover {
            background-color: #B2DFDB;
            transform: translateY(-3px);
        }

        ul {
            list-style-type: none;
        }

        li {
            margin-bottom: 20px;
            background-color: #FFF;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        img {
            border-radius: 50%;
            margin-right: 20px;
            display: inline-block; /* Display as inline-block for side-by-side layout */
            vertical-align: middle; /* Aligns the middle of the image with the adjacent text */
        }
        
        
        .username-highlight {
            font-size: 1.5em;
            color: #00796B;
            display: inline-block;
            margin: 20px 0;
            border-bottom: 2px solid #00796B;
        }
        
        /* Logout button styles */
        .logout-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #E0F2F1;
            color: #00796B;
            padding: 8px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-decoration: none;
        }

        .logout-button:hover {
             background-color: #B2DFDB;
            transform: translateY(-3px);
        }
    
    </style>
</head>
<body>
<a href="index.php" class="logout-button">Logout</a>


<h2>collectiveleaf</h2>

<h3>Profile</h3>
<img src="display_icon.php?user_id=<?= $user["id"] ?>" alt="User icon" width="100">
<p>Name: <?= $user["username"] ?></p>
<a href="edit_profile.php">Edit Profile</a>

<h3>My Bookshelf</h3>
<ul>
<?php
foreach ($books as $book) {
    echo "<li>";
    echo "<strong>" . htmlspecialchars($book["title"]) . "</strong> — " . htmlspecialchars($book["author"]);
    echo "<br><a href='view_memo.php?book_id=" . $book['id'] . "'>View Notes</a>";
    echo "</li>";
}
?>
</ul>

<h3>Search Books</h3>
<a href="search_books.php">Search</a>

</body>
</html>

