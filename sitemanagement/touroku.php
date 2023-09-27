<?php
//管理者登録用
session_start();

chdir("../"); //カレントディレクトリの変更
require_once './module/functions.php';
require_once "./module/connect.php";


// $id = "testmanager@gmail.com";
// $id = "mokumoku8989@gmail.com";
$id = "web22k2g2@gmail.com";
// $id = "web22g2@websystem.rulez.jp";
$pass = "123456789";

echo "管理者メールアドレス：".$id."<br>";
echo "管理者パスワード：".$pass;

$arr = array();
$arr[] = hash("sha256",$id);	//メールアドレスをハッシュ化
$arr[] = password_hash($pass, PASSWORD_DEFAULT);	//パスワードハッシュ化
$sql = "insert into k2g2_manager(user_id,pass,manager_create_date) values(?,?,now())";

db_execution($sql, $arr);



