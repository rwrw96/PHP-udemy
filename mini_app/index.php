<?php 
session_start();
require('dbconnect.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
  $_SESSION['time'] = time();

  $members = $db -> prepare('SELECT * FROM members WHERE id=?');
  $members -> execute(array($_SESSION['id']));
  $member = $members -> fetch();
} else {
  header('Location: login.php');
  exit();
}

if(!empty($_POST)) {
  if($_POST['message'] !== ''){

    $message = $db -> prepare('INSERT INTO posts SET member_id=?, message=?, repry_message_id=?, created=NOW()');
    $message -> execute(array(
      $member['id'],
      $_POST['message'],
      $_POST['reply_post_id']
    ));
    header('Location: index.php');
  }
}

$posts = $db -> query('SELECT members.name, members.picture, posts.* FROM members, posts
                      WHERE members.id = posts.member_id ORDER BY posts.created DESC');

if(isset($_REQUEST['res'])){
  $response = $db -> prepare('SELECT members.name, members.picture, posts.* FROM members, posts
  WHERE members.id = posts.member_id AND posts.id=?');
  $response -> execute(array($_REQUEST['res'])); 
  $table = $response -> fetch();

  $message = '@' . $table['name'] . ' ' . $table['message'];  
}
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
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <dt><?PHP print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
        <?php print($_POST['message']); ?>
        <?php var_dump($_REQUEST['res']); ?>
        <dd>
          <textarea name="message" cols="50" rows="5"><?php print($message); ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php print($_REQUEST['res']); ?>" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>

    <?php foreach($posts as $post): ?>
    <div class="msg">
    <img src="member_picture/<?php print($member['picture']); ?>" width="48" height="48" alt="<?php print($post['name']); ?>" />
    <p><?php print($post['message']); ?><span class="name">（<?php print($post['name']); ?>）</span>[<a href="index.php?res=<?php print($post['id']); ?>">Re</a>]</p>
    <p class="day"><?php print($post['created']); ?><a href="view.php?id="></a>
<a href="view.php?id=">返信元のメッセージ</a>
[<a href="delete.php?id="
style="color: #F33;">削除</a>]
    </p>
    </div>
    <?php endforeach; ?>

<ul class="paging">
<li><a href="index.php?page=">前のページへ</a></li>
<li><a href="index.php?page=">次のページへ</a></li>
</ul>
  </div>
</div>
</body>
</html>
