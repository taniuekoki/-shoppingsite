<?php 

//2023-02-27 fujimoto メール内容修正
session_start();


include("./module/connect.php");
//require_once './module/UserLogic.php';

$customer_code = "";
$customer_name = "";
$cate1_arr = [ 'item' => "商品について", 'system' => "システムについて", 'account' => "アカウントについて"];
// $email = "";
$cate2 = "";
$arr = array();

// ログインしていたら
if(isset($_SESSION['login_user']['user_code'])){
	//ログイン中のカスタマ連番を入力
	$customer_code = $_SESSION['login_user']['user_code'];
	$customer_name = $_SESSION['login_user']['user_name'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_SESSION["contact_ticket"]) && isset($_POST["contact_ticket"])){
		if($_POST["contact_ticket"] == $_SESSION["contact_ticket"]){
			
			
			$email = htmlspecialchars($_POST["email"], ENT_QUOTES);
			$cate2 = $_POST["cate2"];
			
			$cate1 = $_POST["category"];
			
			foreach($cate1_arr as $key => $val){
				if( $key == $cate1){
					$cate1 = $val;
				}
			}
// 			echo $email."<br>";
// 			echo $cate1."<br>";
// 			echo $cate2."<br>";
			
			
			/**問い合わせ完了メールを送信**/
			
			//エンコード
			mb_language("Japanese");
			mb_internal_encoding("UTF-8");
			
			//ユーザーメールアドレス平文
			$to = $email;
		// 	$to = $_SESSION['login_user']['user_email'];
			
			//メールタイトル
			$subject = "[Picstock]お問い合わせ完了メール";
			
			//本文
			$message = "お問い合わせを受け付けました\n
						受付日：".date('Y年n月j日 G時i分')."\n
						カテゴリ：".$cate1."\n
						お問い合わせ内容：".$cate2."\n
						\n
						Picstock\n
						http://websystem.rulez.jp/22/web22g2/";
			
			//サイトからの送信元メールアドレス
			//$headers = "From: ". $email;
			$headers = "From: " .mb_encode_mimeheader("Picstock") ."<web22g2@websystem.rulez.jp>";
			
			
			//メール送信実行
			$result2 = mb_send_mail($to, $subject, $message, $headers);
			
			if(!$result2){
				//メール送信失敗したら
				$_SESSION['m_msg'] = "メール送信に失敗しました";
				header("Location:./contact.php");
				exit();
			}else{
				//メール送信出来たら
				echo '<script>alert("お問い合わせを受け付けました。")</script>';
		// 		header("Location:./contact.php");
		// 		exit();
				
				//ログイン中のカスタマ連番で照会
				$arr[] = $customer_code;
				$arr[] = $customer_name;
				$arr[] = $cate1;
				$arr[] = $cate2;
				
				$sql = "INSERT INTO k2g2_contact (customer_code, nickname, contact_date, contact_genre, contact_details) VALUES (?, ?, now(), ?, ? )";
				
				$result_con = db_execution($sql,$arr);
				
				//DB失敗してたらエラー表示
				if($result_con === FALSE){
					$_SESSION['d_msg']  = "DBエラー：接続に失敗しました。";
					header("Location:./contact.php");
					exit();
				}
				
				
			}
		}
	}
	if(isset($_SESSION["contact_ticket"])){
		unset($_SESSION["contact_ticket"]);
	}
}
	//二重送信防止トークン
	$ticket = uniqid();
	$_SESSION["contact_ticket"] = $ticket;
	
	


?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>お問い合わせ</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/contact.css">
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/contact.js"></script>
  
</head>

<body>
	<div class="innerWrap">
		<header class="header">
		<?php include "header.php" ?>
		</header>
		
		<main>
		
			<h2>お問い合わせ</h2>
			
			<?php 
			
			if(isset($_SESSION['m_msg'])){
				$m_msg = $_SESSION['m_msg']; 
				echo "<script>alert(' $m_msg ')</script>";
				unset($_SESSION['m_msg']);
			}
			
			
			
			if(isset($_SESSION['d_msg'])){
				$d_msg = $_SESSION['d_msg']; 
				echo '<script>alert(' .$_SESSION['d_msg']. ')</script>';
				unset($_SESSION['d_msg']);
			}
			?>
			
			
			<form id="conform" action="<?= $_SERVER["SCRIPT_NAME"]?>" name="conform" method="post">
				<div class="form">
					<div id="mailbox">
						<p>お問い合わせ返信用メールアドレス<span class="alert">&emsp;**正しく入力してください**</span></p>
						<input id="email" type="email" name="email" value="" ><br>
					</div>
					
					<div id="selectbox1">
						<p>カテゴリ<span class="alert">&emsp;**選択してください**</span></p>
						<div class="selectwrapp2">
							<select id="menu3" class="cateselect"  name="category">
								<option value="none">---選択してください---</option>
								<option value="item">商品について</option>
								<option value="system">システムについて</option>
								<option value="account">アカウントについて</option>
							</select>
						</div>
					</div>
					
					<div id="selectbox2">
						<p>お問い合わせ詳細<span class="alert">&emsp;**選択してください**</span></p>
						<div class="selectwrapp2">
							<select id="menu4" class="cateselect" name="tag" form="tag_seach">
								<option value="none">---選択してください---</option>
								<input type="hidden" name="cate2" value="">
							</select>
						</div>
					</div>
					<input type="hidden" name="contact_ticket" value="<?= $ticket ?>">
					<div class="bt">
						<a class="ab" href="index.php">TOPに戻る</a>
						<input id="conbtn" type="button" value="問い合わせる">
					</div>
				</div>
			</form>
		</main>
		
		<footer>
			<?php include "footer.php" ?>
		</footer>
	</div>

</body>
</html>