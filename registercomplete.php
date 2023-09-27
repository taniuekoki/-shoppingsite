<?php
//最終更新日2023-01-23 02:26AM

//修正 2023-01-28 fujimoto
session_start();
require_once './module/UserLogic.php';

// エラーメッセージ
$err = [];

/**********トークンチェック**********/
$token = filter_input(INPUT_POST, 'csrf_token');
//トークンがない、もしくは一致しない場合、処理を中止して新規登録へ飛ばす
if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
	header('Location: register.php');
	exit;
}

unset($_SESSION['csrf_token']);

/**********トークンチェックここまで**********/

// バリデーション
if(!$nickname = filter_input(INPUT_POST, 'nickname')) {
	$err['nickname'] = 'ニックネームを記入してください。';
}

$user_id = filter_input(INPUT_POST, 'user_id');
if(!$user_id = filter_input(INPUT_POST, 'user_id')) {
	$err['user_id'] = 'メールアドレス（ID）を記入してください。';
}

$user_id_conf = filter_input(INPUT_POST, 'user_id_conf');
if ($user_id !== $user_id_conf) {
	$err['user_id_conf'] = '確認用メールアドレスと異なっています。';
}





$pass = filter_input(INPUT_POST, 'pass');
// 正規表現
if (!preg_match("/\A[a-z\d]{8,32}+\z/i",$pass)) {
	$err['pass'] = 'パスワードは英数字8文字以上32文字以下にしてください。';
}

$pass_conf = filter_input(INPUT_POST, 'pass_conf');
if ($pass !== $pass_conf) {
	$err['pass_conf'] = '確認用パスワードと異なっています。';
}

if (count($err) === 0) {
	// 仮登録をする
	$hasCreated = UserLogic::temporaryRegistration($_POST);
	
	if (!empty($_SESSION['msg'])) {
		$err['overlap'] = $_SESSION['msg'];
		$_SESSION['msg'] = array();
	}
	if(!$hasCreated) {
		$err[] = '登録に失敗しました';
	}
}

if (count($err) > 0) {
	// エラーがあった場合は戻す
	$_SESSION["register_err"] = $err;
	$_SESSION["register_userdata"]["nickname"]= $_POST["nickname"];
	$_SESSION["register_userdata"]["user_id"]= $_POST["user_id"];
	$_SESSION["register_userdata"]["user_id_conf"]= $_POST["user_id_conf"];
	
	header('Location: register.php');
	return;
	
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>仮登録完了</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/logout.css">
  <script src="js/jquery-2.1.4.min.js"></script>
</head>

<body>
	<div class="innerWrap">
		<header class="header">
			<?php include "header.php" ?>
		</header>
	<main>
		<?php if (count($err) > 0) : ?>
	  	<?php foreach($err as $e) : ?>
	    <p><?php echo $e ?></p>
	  	<?php endforeach ?>
		<?php else : ?>
	  	<p>ご登録されたメールアドレスに、仮登録メールを送信しました。</p>
		<?php endif ?>
		<div class="aa">
				<span><a href="index.php">トップ画面</a></span>   
				<span><a href="login.php">ログイン</a></span>
			</div>
	</main>
<footer>
			<?php include "footer.php" ?>
	</footer>
</body>
</html>