<?php
//2023-02-08 10：30 fujimoto アカウントの変更ページを削除
session_start();
require_once './module/UserLogic.php';
require_once './module/functions.php';

//　ログインしているか判定し、していなかったら新規登録画面へ返す
$result = UserLogic::checkLogin();

if (!$result) {
	$_SESSION['login_err'] = 'ログインしてください！';///////////
	header('Location: login.php');
	return;
}

$login_user = $_SESSION['login_user'];/////////////

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="./css/mypage.css">
  <script src="js/jquery-2.1.4.min.js"></script>
  <title>マイページ</title>
</head>
<body>
	
	<div class="innerWrap">
		  <header class="header">
		      <?php include "header.php" ?>
		  </header>
		  
		  <main>
			<div class="myin">
			<?php //ユーザー情報表示 ?>
				<h2>マイページ</h2>
				<ul class="list">
					<li>・<a href="purchasehistory.php">購入履歴・ダウンロード</a></li>
					<li>・<a href="favorite.php">お気に入り</a></li>
					<li>・<a href="contact.php">お問い合わせ</a></li>
					<!-- <li class="hoge"></li> -->
					<li>・<a href="logout.php">ログアウト</a></li>
				</ul>
			</div>
			
			
		  </main>
		</div>
<footer>
			<?php include "footer.php" ?>
	</footer>
</body>
</html>