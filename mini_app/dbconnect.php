<?php
try {
    $db = new PDO('mysql:dbname=mini_app;host=localhost;charset=utf8', 'root', 'root');
} catch(PDOException $e) {
    print('接続エラー:' . $e -> getMessage());
}
?>