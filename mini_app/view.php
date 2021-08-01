<?php 
session_start();
require('dbconnect.php');

if (empty($_REQUEST)){
  header('Location: index.php');
  exit();
}
$posts= $db -> prepare('SELECT members.name, members.picture, posts.* FROM members, posts WHERE members.id = posts.member_id AND posts.id= ?');
$posts -> execute(array($_REQUEST['id']));
// $post = $message -> fetch();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  <p>&laquo;<a href="index.php">一覧にもどる</a></p>

  <?php if($post = $posts -> fetch()): ?>
    <div class="msg">
    <img src="member_picture/<?php print($post['picture']); ?>" />
    <p><span class="name">（<?php print($post['name']); ?>）</span><?php print($post['message']); ?></p>
    <p class="day"><?php print($post['created']); ?></p>
    </div>
    <?php else: ?>
    <p>その投稿は削除されたか、URLが間違えています</p>
  <?php endif; ?>
  </div>
</div>
</body>
</html>
