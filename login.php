<?php 
include 'dbconnect.php'; 
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - collectiveleaf</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #224632; /* Dark forest green background */
            color: #E9E3CF; /* Off white text color for better readability against dark background */
            text-align: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            font-size: 2em;
            margin-bottom: 30px;
        }

        form {
            background-color: #456545; /* Darker green form background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.5);
            display: inline-block;
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            border-radius: 5px;
            border: none;
            margin-bottom: 10px;
            width: 200px;
        }

        input[type="submit"] {
            background-color: #E9E3CF; /* Off white button color */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #C7C1A8; /* Lighter hover button color */
        }

        p.error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
        
        .back-button {
            display: inline-block;
            margin-top: 20px;
             padding: 10px 20px;
            background-color: #E9E3CF; /* Off white button color */
            border: none;
            border-radius: 5px;
            text-decoration: none; /* Remove underline from link */
            color: #224632; /* Dark forest green text color */
            transition: background-color 0.3s ease;
        }

    </style>
</head>
<body>
    <h2>Signin to collectiveleaf</h2>

    <form action="login_process.php" method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" value="Signin">
    </form>

    <?php
    if (isset($_GET['error']) && $_GET['error'] == 1) {
        echo '<p class="error">ユーザー名またはパスワードが正しくありません。</p>';
    }
    ?>
    
     <a href="index.php" class="back-button">Return</a>

</body>
</html>
