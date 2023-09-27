<?php
//最終更新日2023-01-23 02:26AM
session_start();
require_once './module/UserLogic.php';



//　ログインしているか判定し、セッションが切れていたらログインしてくださいとメッセージを出す。
$result = UserLogic::checkLogin();
if (!$result) {
	header('Location: index.php');
	return;
}

// ログアウトする
UserLogic::logout();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ログアウト</title>
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
		<h2>ログアウトしました。</h2>
		
		<div class="aa">
			<span><a href="index.php">トップに戻る</a></span>
			<span><a href="login.php">ログイン</a></span>
		</div>
	</main>
</div>
<footer>
			<?php include "footer.php" ?>
	</footer>

</body>
</html>