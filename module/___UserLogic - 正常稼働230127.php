<?php
//最終更新日2023-01-23 02:26AM

require_once "./module/connect.php";		//connect.phpファイル読み込み

//ログイン関連に使用するクラス
class UserLogic
{
	/**
	 * ユーザを登録する
	 * @param array $userData
	 * @return bool $result
	 */
	public static function createUser($userData)
	{
		$result = false;
		
		$db_user = self::getUserByEmail($userData['user_id']);
		
		if (!empty($db_user)) {
			$_SESSION['msg'] = 'メールアドレス（ID）が既に登録済みです。';
			return $result;
		}
			// ユーザデータを配列に入れる
		$arr = [];
		//$arr[] = str_repeat('あ', '64');				//ニックネーム処理どうする？？？？？？？？？？？？？？？？？？
		$arr[] = $userData['nickname'];
		//$arr[] = $userData['user_id'];
		$arr[] = hash("sha256",$userData['user_id']);	//メールアドレスをハッシュ化
		//$arr[] = $userData['user_id'];
		$arr[] = password_hash($userData['pass'], PASSWORD_DEFAULT);	//パスワードハッシュ化
		
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
			$_SESSION['msg'] = 'email（ID）が一致しません。';
			return $result;
		}
		
		//ユーザーレコードをフェッチ
		$user = $db_user->fetch();
		
		//　パスワードの照会
		if (password_verify($pass, $user['pass'])) {
			//ログイン成功
			session_regenerate_id(true);///////セッションIDの更新

			//ログイン成功したので、セッション変数にカスタマ連番とニックネーム保管
			$_SESSION['login_user']['user_code'] = $user['customer_code'];
			$_SESSION['login_user']['user_name'] = $user['nickname'];

			//ログイン成功を返す
			$result = true;
			return $result;
		}
		
		$_SESSION['msg'] = 'パスワードが一致しません。';
		return $result;
	}
	
	/**
	 * emailからユーザを取得
	 * @param string $email
	 * @return array|bool $user|false
	 */
	public static function getUserByEmail($user_id)
	{
		// SQLの準備
		// SQLの実行
		// SQLの結果を返す
		
		$sql = 'SELECT * FROM k2g2_customer WHERE user_id = ?';

		//ハッシュ化したemailを配列に入れる
		$arr = array();		
		$arr[] = hash("sha256",$user_id);
		//$arr[] = $user_id;	//検証用平文処理
		
		$user = db_execution($sql,$arr);

		//返りが0件ならメールアドレスが相違
		if($user -> rowCount() == 0){
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
	
}