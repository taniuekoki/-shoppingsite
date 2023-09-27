<?php
//本登録認証ページ
//2023-01-28 fujimoto
//2023-01-31 yoshinaga 		HTML統合
session_start();
require_once './module/UserLogic.php';
require_once "./module/connect.php";

$err = "";
//$userData['nickname'] = "test";　テスト様

//本当はトランザクションしたいけど・・・

//URLのGETチェック
if(!isset($_GET["token_code"]) || !strlen($_GET["token_code"])){
	$err= "不正なURLです";
}else{
	//GET値あり
	try{
		$result2 = false;
		
		//トークン合致チェック
		$arr = array();
		$arr[] = $_GET["token_code"];
		
		$sql = 'select * from k2g2_draft_customer where draft_token = ?';
		$result = db_execution($sql,$arr);
		
		if($result->rowCount() == 1){	//レコード数1ならトークン合致
			
// 			echo "認証完了";
			
			$row = $result->fetch();
			
			//本登録
			$userData = array();
			$userData['nickname'] = $row["draft_nickname"];
			$userData['user_id'] = $row["draft_user_id"];
			$userData['pass'] = $row["draft_pass"];
			
			$result2 = UserLogic::createUser($userData);
			
			if($result2 != false){
// 				echo "削除開始";
				//仮登録レコード削除
				$arr3 = array();
				$arr3[] = $_GET["token_code"];
				$sql = 'delete from k2g2_draft_customer where draft_token = ?';
				$result3 = db_execution($sql,$arr);
				
				if($result3 === false){	
					$err = "サーバーエラーです";
				}
			}else{
				$err = "サーバーエラーです";
			}
			
		}else{
			//$db->rollBack();
			$err = "不正なURLです";
		}
	}catch(PDOException $e){
		$err = "サーバーエラーです";
	}
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>新規登録メールURL</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/registercheck.css">
  <script src="js/jquery-2.1.4.min.js"></script>

</head>
<body>

	<div class="innerWrap">
		<header class="header">
			<?php include "header.php" ?>
		</header>
	</div>
		
		<main>
		<?php
		//エラーメッセージ無ければ表示(完了時)
		if(!strlen($err)){
			?>
		<h1>登録完了</h1>
			
			<p><?php echo $userData['nickname'];?>さん本登録が完了しました。</p>
			
			<div class="aa">
				<span><a href="index.php">トップに戻る</a></span>
				<span><a href="login.php">ログイン</a></span>
			</div>
		<?php
		}else{ 
			//エラーメッセージ有れば表示
			?>
			<h2>登録できませんでした</h2>
			
			<p><?php echo $err;?></p>
			
			<div class="aa">
				<span><a href="index.php">トップに戻る</a></span>
			</div>
			<?php 
		}
		?>
		
	</main>
	<div class="innerWrap">
		<footer>
			<?php include "footer.php"?>
		</footer>
	</div>
  </body>
</html>
