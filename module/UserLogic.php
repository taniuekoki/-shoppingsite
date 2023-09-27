<?php
//最終更新日2023-01-28 fujimoto
//最終更新日2023-02-08 fujimoto 自動退会クラスを追加
require_once "./module/connect.php";		//connect.phpファイル読み込み

//ログイン関連に使用するクラス
class UserLogic
{
	/**仮登録クラス
	/**
	 * @param array $userData
	 * @return boolean
	 */
	public static function temporaryRegistration($userData)
	{
		/**既存チェック**/
		//$chkUser = hash("sha256",$userData['user_id']);
		$chkUser = $userData['user_id'];
		$db_user = self::getUserByEmail($chkUser);
		
		if (!empty($db_user)) {
			$_SESSION['msg'] = 'メールアドレス（ID）が既に登録済みです。';
			return false;
		}
		
		/**仮登録テーブルへ保存**/
		
		$result1 = $result2 = false;
		
		//トークン生成
		$TOKEN_LENGTH = 32;	//バイト数設定	32byte->64文字
		$bytes = random_bytes($TOKEN_LENGTH);
		
		//メール送信用トークン
		$token = bin2hex($bytes); 
		
		//仮登録データベースに登録
		$arr = array();
		
		$arr[] = $userData['nickname'];					//ニックネーム
		$arr[] = hash("sha256",$userData['user_id']);	//メールアドレスをハッシュ化
		$arr[] = password_hash($userData['pass'], PASSWORD_DEFAULT);	//パスワードハッシュ化
		$arr[] = $token;
		
		$sql = 'INSERT INTO k2g2_draft_customer (draft_nickname, draft_user_id, draft_pass,draft_token,draft_date) VALUES (?, ?, ?, ?, now())';
		
		$result1 = db_execution($sql,$arr);
		
		if(!$result1){
			$_SESSION['msg'] = "サーバーエラーです。";
			return false;
		}
		
		
		/**仮登録メールを送信**/
		
		//エンコード
			mb_language("Japanese");
			mb_internal_encoding("UTF-8");
		
		//本登録アクセス用URL生成
			$url = "http://localhost/web/k2/registercheck.php?token_code=".$token;
		
		//ユーザーメールアドレス平文
 			$to = $userData['user_id'];	
 		
 		//メールタイトル
			$subject = "[技専校]仮登録メール";
		
		//本文
			$message = $userData['nickname']."さんユーザー登録ありがとうございます。\r\n以下のURLにアクセスし、本登録を完了させてください。\r\n".$url;
		
		//サイトからの送信元メールアドレス
			$headers = "From: web2219@websystem.rulez.jp";
// 			$headers = "From: web22g2@websystem.rulez.jp". "\r\n";
// 			$headers .= 'Return-Path: mock8989@yahoo.co.jp';
		
		//メール送信実行
			$result2 = mb_send_mail($to, $subject, $message,$headers); 
		
		if(!$result2){
			$_SESSION['msg'] = "メール送信に失敗しました";
			
			//失敗したレコードを削除
			$arr = array();
			$arr[] = $token;
			$sql = 'delete from k2g2_draft_customer where draft_token = ?';
			db_execution($sql,$arr);
			
			return false;
		}
		
		return true;
		
	}
	
	
	
	
	/**
	 * ユーザを登録する
	 * @param array $userData
	 * @return bool $result
	 */
	public static function createUser($userData)
	{
		$result = false;
		/*
		$db_user = self::getUserByEmail($userData['user_id']);
		
		if (!empty($db_user)) {
			$_SESSION['msg'] = 'メールアドレス（ID）が既に登録済みです。';
			return $result;
		}
		*/
			// ユーザデータを配列に入れる
		$arr = [];
		$arr[] = $userData['nickname'];	//ニックネーム
		
		$arr[] = $userData['user_id'];	//仮テーブルでハッシュ化してるのでuser_idそのまま入れる
		
		$arr[] = $userData['pass'];	//仮テーブルでハッシュ化してるのでパスワードそのまま入れる
		
		$sql = 'INSERT INTO k2g2_customer (nickname, user_id, pass) VALUES (?, ?, ?)';
		
		$result = db_execution($sql,$arr);
		return $result;
		
	}
	
	/**
	 * ログイン処理
	 * @param string $email
	 * @param string $password
	 * @return bool $result
	 */
	public static function login($user_id, $pass)
	{
		// 結果
		$result = false;
		// ユーザをemailから検索して取得
		$db_user = self::getUserByEmail($user_id);
		
		if (!$db_user) {
			$_SESSION["login_err"]['user_id'] = 'メールアドレス(ID)が間違っているか存在しません。';
			return $result;
		}
		
		//ユーザーレコードをフェッチ
		$user = $db_user->fetch();
		
		//パスワードの照会
		if (password_verify($pass, $user['pass'])) {
			//ログイン成功
			session_regenerate_id(true);///////セッションIDの更新

			//ログイン成功したので、セッション変数にカスタマ連番とニックネーム保管
			$_SESSION['login_user']['user_code'] = $user['customer_code'];
			$_SESSION['login_user']['user_name'] = $user['nickname'];
			$_SESSION['login_user']['user_email']= $user_id;//メールアドレス平文をセッションにいれる

			//ログイン成功を返す
			$result = true;
			return $result;
		}
		
		$_SESSION["login_err"]['pass'] = 'パスワードが間違っています。';
		return $result;
	}
	
	/**
	 * emailからユーザを取得
	 * @param string $email
	 * @return array|bool $user|false
	 */
	public static function getUserByEmail($user_id)
	{
		//古いユーザーを削除するバッチ処理
		self::auto_deleted_id();
		
		// SQLの準備
		// SQLの実行
		// SQLの結果を返す
		
		$sql = 'SELECT * FROM k2g2_customer WHERE user_id = ?';

		//ハッシュ化したemailを配列に入れる
		$arr = array();		
		$arr[] = hash("sha256",$user_id);
		
		$user = db_execution($sql,$arr);

		//返りが0件ならメールアドレスが相違
		if($user->rowCount() == 0){
			return false;
		}

		//ユーザーレコードを返す
		return $user;
	}
	
	/**
	 * ログインチェック
	 * @param void
	 * @return bool $result
	 */
	public static function checkLogin()
	{
		$result = false;
		
		// セッションにログインユーザが入っていなかったらfalse
		if (isset($_SESSION['login_user'])) {
			return $result = true;
		}
		
		return $result;
		
	}
	
	/**
	 * ログアウト処理
	 */
	public static function logout()
	{
		$_SESSION = array();
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}
	
	
	/**
	 * 自動退会バッチ処理
	 */
	public static function auto_deleted_id(){
		$duration_day = 31;	//退会までの期間を指定
		$arr = array();
		$sql = "DELETE FROM k2g2_customer WHERE register_date < DATE_SUB(now(), INTERVAL ? day)";
		$arr[] = $duration_day;
		
		db_execution($sql,$arr);
	}
	
}