<?php 
//2023-02-21 15:00 fujimoto 初期設計

header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', FALSE );
header('Pragma:no-cache');

session_start();

chdir("../"); //カレントディレクトリの変更
require_once './module/functions.php';
require_once "./module/connect.php";

$errmsg = array();
$check = false;	//初期フォームかMFA画面にするか
$relogin = false; //再ログインボタンを表示させる


//DBサーバー稼働チェック
if(is_null($db) || isset($_SESSION["errmsg"])){
	$errmsg["server"]= "サーバーエラー発生";
	unset($_SESSION["errmsg"]);
}else{
	
	
	
	if ($_SERVER["REQUEST_METHOD"] == "GET"){
		$_SESSION = null;
	}
	
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		$token = filter_input(INPUT_POST, 'csrf_token');
		//トークンがない、もしくは一致しない場合、処理を中止してログインへ飛ばす
		if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
			header('Location: ./managerlogin.php');
			exit;
		}
		unset($_SESSION['csrf_token']);
		
		//ログイン認証
		if(isset($_POST["user_id"]) && isset($_POST["user_pass"]) ){
	
			$result = getUserByEmail($_POST["user_id"]);
			if($result){
				//ユーザーレコードをフェッチ
				$user = $result->fetch();
				
				//パスワードの照会
				if (password_verify($_POST["user_pass"], $user['pass'])) {
					$_SESSION["manager_code"] = $user["manager_code"];
					$_SESSION["manager_mail"] = $_POST["user_id"];
					
					//DBチェック通過なので、二要素認証へ
					$check = true;
					$arr = array();
					$mfa_token =  rand(100000,999999); //MFAトークン発行
					$arr[] = password_hash($mfa_token, PASSWORD_DEFAULT);	//MFAトークンハッシュ化
					$arr[] = hash("sha256",$_POST["user_id"]);
					$sql = "update k2g2_manager set manager_token = ? , token_limit = DATE_ADD(NOW(), INTERVAL 3 MINUTE) WHERE user_id = ?";
					$result2 = db_execution($sql, $arr);
					
					if($result2 === false){
						$errmsg["server"] = "サーバーエラーです";
					}
					
					echo "トークン値:".$mfa_token;
					
					/**認証メールを送信する**/
					$mailaddress = $_POST["user_id"];
					
					//有効期限
					$mail_token_limit = date("Y-m-d H:i:s",strtotime("3 minute"));; 
					
					//エンコード
					mb_language("Japanese");
					mb_internal_encoding("UTF-8");
					
					
					//ユーザーメールアドレス平文
					$to = $mailaddress;
// 					$to = "web2219@domainweb.local";
// 					$to = "mokumoku8989@gmail.com";
					
					//メールタイトル
					$subject = "[Picstock]認証コード:".$mfa_token;
					
					//本文
					$message = "Picstockのログイン認証コード:".$mfa_token."\n有効期限:".$mail_token_limit."\n\nこのメールには返信できません。";
					
					//サイトからの送信元メールアドレス
					//$headers = "From: web22g2@websystem.rulez.jp";
					$headers = "From: " .mb_encode_mimeheader("Picstock") ."<no-reply@websystem.rulez.jp>";
					// 			$headers = "From: web22g2@websystem.rulez.jp". "\r\n";
					// 			$headers .= 'Return-Path: web22g2@websystem.rulez.jp';
					
					//メール送信実行
					$result2 = mb_send_mail($to, $subject, $message,$headers);
					
					if(!$result2){
						$check = false;
						$errmsg["server"] = "メール送信に失敗しました";
					}
					/**メールを送信する**/
					
					//試行回数設定
					$_SESSION["try_limit"] = 3;
				
				}else{
					$errmsg["pass"] = "パスワードが異なっています";
				}
				
			}elseif(isset($_SESSION["errmsg"])){
				$errmsg["server"] = "取得に失敗しました";
			}else{
				$errmsg["id"] = "IDが異なっています";
			}
			
		}
		
		//MFA認証
		if(isset($_POST["mfacode"])&& isset($_SESSION["manager_code"])){
			$_SESSION["try_limit"]--;
			$check = true;
			$arr = array();
			
			$arr[] = $_SESSION["manager_code"];
			
			$sql = "select manager_token,case when token_limit > now() then 1 else 0 end as 'date_check' from k2g2_manager where manager_code = ?";
			$result3 = db_execution($sql, $arr);
			$db_token = $result3->fetch();
			
			if($db_token["date_check"]){
				//MFAトークン照合
				if (password_verify($_POST["mfacode"], $db_token["manager_token"])) {
					//成功。ログイン処理。
					$_SESSION["admin_user"]["user_id"] = $_SESSION["manager_mail"];	//ログインチェック用セッション変数;
					$_SESSION["admin_user"]["manager_code"] = $_SESSION["manager_code"];
					
					unset($_SESSION["try_limit"]);		//ログイン中の変数消去
					unset($_SESSION["manager_code"]);	//ログイン中の変数消去
					unset($_SESSION["manager_mail"]);	//ログイン中の変数消去
					//管理者メニューへ飛ばす
					header("Location:./managermenu.php");
					exit();
				
				}elseif($_SESSION["try_limit"]>0){
						$errmsg["token"] = "認証番号が正しくありません";
				}else{
						$errmsg["token"] = "試行制限回数を超えました<br>再度ログインしてください";
						$relogin = true;
						unset($_SESSION["manager_code"]);
				}
			}else{
				$errmsg["token"] = "有効期限が切れています<br>再度ログインしてください";
				$relogin = true;
				unset($_SESSION["manager_code"]);
			}
			
		}
	}
	
}//dbエラー分岐終了	




//DBチェックファンクション
function getUserByEmail($user_id)
{

	$sql = 'SELECT * FROM k2g2_manager WHERE user_id = ?';
	
	//ハッシュ化したemailを配列に入れる
	$arr = array();
	$arr[] = hash("sha256",$user_id);
	
	$user = db_execution($sql,$arr);
	
	if($user === false){
		$_SESSION["errmsg"] = "サーバーエラーです";
		return false;
	}
	
	//返りが0件ならメールアドレスが相違
	if($user->rowCount() == 0){
		return false;
	}
	
	//ユーザーレコードを返す
	return $user;
}

?>




<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Picstock - Management</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/managerlogin.css">
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/jquery.validate.min.js"></script>
  <script src="js/managerlogin.js"></script>
</head>
<body>
    <main>
        <a href="./managermenu.php"><img id="logo" src="../images/top1.png"></a>
        <h1>管理ページログイン</h1>
        
        
        
        <?php if(!$check){ //初期ログインフォーム?>
        <div class="form_area">
            <form action="managerlogin.php" id="login_form" method="POST">
                ID(メールアドレス)<br>
                <input type="email" id="user_id" name="user_id" autofocus value="<?php if(isset($_POST["user_id"])) echo $_POST["user_id"];?>">
                <p class="err_msg is-error-user_id"><?php if(isset($errmsg["id"])) echo $errmsg["id"];?></p>
                PASS<br>
                <input type="password" id="user_pass" name="user_pass" value="">
                <p class="err_msg is-error-user_pass"><?php if(isset($errmsg["pass"])) echo $errmsg["pass"];?></p>
                <input type="hidden" name="csrf_token" value="<?php echo h(setToken()); ?>">
                <button type="submit" id="submit_btn" name="submit_btn">管理者ログイン</button>
                <p class="err_msg err_server"><?php if(isset($errmsg["server"])) echo $errmsg["server"];?></p>
            </form>
        </div>
        
        
        
        <?php }else{ //二要素認証エリア?>
        <div class="form_area">
        <p id="mfamsg" class="center">入力されたメールアドレスへ認証番号を送信いたしました。<br><b>認証番号の有効期限は3分間です。</b></p>
        	<form action="managerlogin.php" id="mfacode_form" method="POST">
        	<p class="center">メールに記載された認証番号を入力してください。</p><br>
        		<input type="text" id="mfacode" name="mfacode" value="" minlength="6" maxlength="6" autocomplete="off" autofocus>
        		<p class="err_msg is-error-mfacode center"><?php if(isset($errmsg["token"])) echo $errmsg["token"];?></p>
        		<input type="hidden" name="csrf_token" value="<?php echo h(setToken()); ?>">
        		
        		<?php if(!$relogin){	//デフォルト：認証ボタン?>
        		<button type="submit" id="submit_btn" name="submit_btn">認証</button>
        		<p class="err_msg err_server"><?php if(isset($errmsg["server"])) echo $errmsg["server"];?></p>
        		
        		<?php }else{	//有効期限切れが判明した場合、再ログインボタンにする?>
        		
        		<button type="button" onclick="location.href='managerlogin.php'" id="submit_btn" class="relogin_btn">再ログイン</button>
        		<p class="err_msg err_server"><?php if(isset($errmsg["server"])) echo $errmsg["server"];?></p>
        		<?php }?>
        	</form>
        
        
        
        </div>
        <?php }?>
    </main>
    <footer>
        &copy;Picstock
    </footer>
</body>
</html>