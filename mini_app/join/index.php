<?php
	session_start();
	require('../dbconnect.php');
	if(!empty($_POST)){
		if($_POST['name'] === ''){
			$error['name'] = brank;
		}
		if($_POST['email'] === ''){
			$error['email'] = brank;
		}
		if(strlen($_POST['password']) < 4){
			$error['password'] = length;
		}
		if($_POST['password'] === ''){
			$error['password'] = brank;
		}

		$fileName = $_FILES['image']['name'];
		if(!empty($fileName)){
			$ext = substr($fileName, -3);
			if($ext != 'jpg' && $ext != 'png'){
				$error['image'] = 'type';
			}
		}
		
		if(empty($error)) {
			$member = $db -> prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
			$member -> execute(array($_POST['email']));
			$record = $member -> fetch();
			if($record['cnt'] > 0) {
				$error['email'] = 'duplicate';
			}
		}
		
		if(empty($error)) {
			$image = date('YmdHis').$_FILES["image"]["name"];
			var_dump(move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'.$image));
			$_SESSION['join'] = $_POST;
			$_SESSION['join']['image'] = $image;
			header('Location: check.php');
			exit();
		}

	}
	if($_REQUEST['action'] == 'rewrite') {
		$_POST = $_SESSION['join'];
	}
		?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>
<div id="content">
<p>次のフォームに必要事項をご記入ください。</p>
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>

		<?php if($error['name'] === brank): ?>
		<p class="error">*名前が入力されていません</p>
		<?php endif; ?>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255" value="<?php print($_POST['name']) ?>" />
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<?php if($error['email'] === brank): ?>
		<p class="error">*メールアドレスが入力されていません</p>
		<?php endif; ?>
		<?php if($error['email'] === 'duplicate'): ?>
		<p class="error">*そのメールアドレスは、既に使用されています</p>
		<?php endif; ?>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?php print($_POST['email']) ?>" />
		<dt>パスワード<span class="required">必須</span></dt>
		<?php if($error['password'] === brank): ?>
		<p class="error">*パスワードが入力されていません</p>
		<?php endif; ?>
		<?php if($error['password'] === length): ?>
		<p class="error">*パスワードは4文字以上でお願いします</p>
		<?php endif; ?>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="<?php print($_POST['password']) ?>" />
        </dd>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
			<?php if($error['image'] === 'type'): ?>
			<p class="error">*拡張子エラーです</p>
			<?php endif; ?>
			<?php if(!empty($error)): ?>
			<p class="error">*もう一度入力お願いします</p>
			<?php endif; ?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
