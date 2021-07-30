<!doctype html>
<html lang="ja">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="../PHP/css/style.css">

<title>PHP</title>
</head>
<body>
<header>
<h1 class="font-weight-normal">PHP</h1>    
</header>

<main>
<h2>Practice</h2>

<?php
    try{
        $db = new PDO ('mysql:dbname=mydb;host=localhost;charset=utf8', 
        'root','root');
    } catch(PDOexception $e) {
        echo 'DB接続エラー' . $e->getMessage();
    }

    $id = $_REQUEST['id'];
    if(!is_numeric($id) || $id <= 0) {
        print('1以上の数字で入力してください');
        exit();
    }


    $memos = $db -> prepare('SELECT * FROM memos WHERE id=?');
    $memos -> execute(array($id));
    $memo = $memos -> fetch();
?>

<article>
    <p><?php print($memo['memo']); ?></p>
    <a href="index.php">戻る</a>
</article>



</main>
</body>    
</html>