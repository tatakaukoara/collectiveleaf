<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - collectiveleaf</title>
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

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5em;
            margin-bottom: 40px;
        }

        a {
            color: #E9E3CF; 
            text-decoration: none;
            background-color: #456545; /* Dark green for buttons */
            padding: 10px 20px;
            margin: 5px;
            border-radius: 3px;
            box-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #678C6A; /* Slightly lighter green for button hover */
        }
    </style>
</head>
<body>
    <h1>CollectiveLeaf</h1>
    <h2>Welcome to the forest of books</h2>
    <div>
        <a href="login.php">Sign in</a>
        <a href="register.php">Sign up</a>
        <br><br>
    </div>
    <br><br>
     <a href="pdfs/CollectiveLeaf.pdf" target="_blank">Aim of the site</a>
</body>
</html>
