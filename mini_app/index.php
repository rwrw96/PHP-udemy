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

$page = $_REQUEST['page'];

if($page == '') {
  $page = 1;
}
$page = max($page, 1);

$counts = $db -> query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts -> fetch();
$maxpage = ceil($cnt['cnt'] / 5);
$page = min($page , $maxpage);

$start = ($page -1) * 5;
$posts = $db -> prepare('SELECT members.name, members.picture, posts.* FROM members, posts
                      WHERE members.id = posts.member_id ORDER BY posts.created DESC LIMIT ?,5');

$posts -> bindparam(1, $start, PDO::PARAM_INT);
$posts -> execute();

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
    <p class="day"><a href="view.php?id=<?php print($post['id']); ?>"><?php print($post['created']); ?></a>
    <?php if(!empty($post['repry_message_id'])): ?>
<a href="view.php?id=<?php print($post['repry_message_id']); ?>">返信元のメッセージ</a>
<?php endif; ?>
<?php if($_SESSION['id'] === $post['member_id']): ?>
[<a href="delete.php?id=<?php print($post['id']); ?>"
style="color: #F33;">削除</a>]
<?php endif; ?>
    </p>
    </div>
    <?php endforeach; ?>

<ul class="paging">
  <?php if($page > 1): ?>
<li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
<?php endif; ?>
<?php if($page < $maxpage): ?>
<li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
<?php endif; ?>
</ul>
  </div>
</div>
</body>
</html>
