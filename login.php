<?php
//最終更新日2023-01-23 02:26AM
session_start();
require_once './module/functions.php';
require_once './module/UserLogic.php';


$result = UserLogic::checkLogin();
if($result) {
	header('Location: mypage.php');
	return;
}
////////////////////////////////////////////////
//$err = $_SESSION["login_err"];
//$_SESSION["login_err"] = array();

$err = isset($_SESSION['login_err']) ? $_SESSION['login_err'] : null;
unset($_SESSION['login_err']);


$login_userdata = isset($_SESSION['login_userdata']) ? $_SESSION['login_userdata'] : null;
unset($_SESSION['login_userdata']);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ログイン画面</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/login.css">
  <script src="js/jquery-2.1.4.min.js"></script>

</head>
<body>
	<div class="innerWrap">
	<header class="header">
	<?php include "header.php" ?>
	</header>
	
<main>
	<div class="innerWrap">
		<h2>ログイン</h2>
		
	    <?php if (isset($err['msg'])) : ?>
        <p style="color:red;"><?php echo $err['msg']; ?></p>
    	<?php endif; ?>
  		<form action="logincomplete.php" method="POST">
		
	<form action="" method="">
		<div class="form">
			<p>メールアドレス(ID)</p>
				<input type="email" name="user_id" value="<?php if (isset($login_userdata['user_id'])) echo $login_userdata['user_id']?>">	
			<?php if (isset($err['user_id'])) : ?>
    		    <p style="color:red;"><?php echo $err['user_id']; ?></p>
    		<?php endif; ?>
				
			<p>パスワード <span style="color:#808080;font-size:small;">英数字8～32文字</span></p>
			<input type="password" name="pass" value="">
			<?php if (isset($err['pass'])) : ?>
        		<p style="color:red;"><?php echo $err['pass']; ?></p>
    		<?php endif; ?>
    		<input type="hidden" name="csrf_token" value="<?php echo h(setToken()); ?>">
		</div>
		<br><br>
		<div class="link">
			<input class="bt" type="submit" name="bt" value="ログイン"><br><br>
		</div>
	</form>
	<div class="link">
		<a href="register.php">アカウントをお持ちでない方はこちら</a><br><br>
		<a href="index.php">TOPへ</a>
	</div>
	</form>
	</div>
	</main>
	</div>
	<footer>
			<?php include "footer.php" ?>
		</footer>
</body>
</html>
