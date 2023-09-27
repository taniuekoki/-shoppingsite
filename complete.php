<?php
//2023-02-06 11:20 fujimoto ログインチェック修正
//2023-02-08 12:00 藤本 遷移チェック追加

session_start();
//ログインチェック
$login_userdata = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : NULL;

//ログインしていなければTOP画面へ
if(!isset($login_userdata)){
	header('Location: index.php');
	return;
}

//セッションpayoutが無ければTOPへ	2023-02-08藤本追加
if(!isset($_SESSION["payout"])){
	header('Location: index.php');
	return;
}else{
	unset($_SESSION["payout"]);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>お支払い完了</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/complete.css">
  <script src="js/jquery-2.1.4.min.js"></script>
</head>

<body>
	<div class="innerWrap">
		<header class="header">
			<?php include "header.php" ?>
		</header>
	<main>
	  	<p>支払い完了</p>
	  	<p2>支払いが完了しました。<br>購入履歴からダウンロード出来ます。</p2>
	  	
		
		<div class="aa">
				<span><a href="purchasehistory.php">購入履歴へ</a></span>
				<span><a href="index.php">トップ画面へ</a></span>
			</div>
		</main>
	<footer>
			<?php include "footer.php" ?>
		</footer>
</body>
</html>