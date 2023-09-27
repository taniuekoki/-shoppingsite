<?php
//最終更新日2023-02-07 13:00
session_start();

require_once './module/UserLogic.php';

// エラーメッセージ
$err = [];

/**********トークンチェック**********/
$token = filter_input(INPUT_POST, 'csrf_token');
//トークンがない、もしくは一致しない場合、処理を中止してログインへ飛ばす
if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
	header('Location: login.php');
	exit;
}

unset($_SESSION['csrf_token']);

/**********トークンチェックここまで**********/

// バリデーション
if(!$user_id = filter_input(INPUT_POST, 'user_id')) {
	$err['user_id'] = 'メールアドレス(ID)を記入してください。';
}
if(!$pass = filter_input(INPUT_POST, 'pass')) {
	$err['pass'] = 'パスワードを記入してください。';
}

if (count($err) > 0) {
	// エラーがあった場合は戻す
	$_SESSION["login_err"] = $err;
	$_SESSION["login_userdata"]["user_id"]= $_POST["user_id"];
	header('Location: login.php');
	return;
}
// ログイン成功時の処理
$result = UserLogic::login($user_id, $pass);
//echo "チェック".var_dump($result); //後で消して！！！！！！！！！！！！！！！！！！！！！！！

// ログイン失敗時の処理
if (!$result) {
	$_SESSION["login_userdata"]["user_id"]= $_POST["user_id"];
	header('Location: login.php');//////////////////////////////////////////////
	return;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ログイン完了</title>
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
		<p><?php echo $_SESSION['login_user']['user_name']."さん" ; ?></p>
			<h2>ログイン完了しました。</h2>

				
		<div class="aa">
			<span><a href="index.php">トップに戻る</a></span>
		</div>
	</main>
</div>
<!-- <form action="mypage.php" method="POST"> -->
<!-- <input type="submit" name="mypage" value="マイページへ"><br> -->

<!-- <input type="submit" name="back" value="前の画面へ戻る"> -->
<?php 
/*
 * 前の画面へ戻る処理
 * スーパーグローバル変数指定後
if($_POST['back']){
	header('Location: ');
	exit;
}*/
?>
<footer>
			<?php include "footer.php" ?>
		</footer>

</body>
</html>