<?php
session_start();
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
	include("./module/connect.php");		//connect.phpファイル読み込み
	
	$arr=array();
	
	$sql = "select * from k2g2_customer";		//実行するsql文を入力
	
	
	$result = db_execution($sql,$arr);      //connect.phpにある関数
	
	while ($row = $result->fetch()) {
		echo "id:".$row["user_id"]." pass:".$row["pass"]."<br>";
	}
	
	if(isset($_SESSION["db_errmsg"])){
		echo $_SESSION["db_errmsg"];
	}
//}
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
// 	include("connect.php");		//connect.phpファイル読み込み

// 	$arr[]=$_POST["pas"];
// 	$arr[]=$_POST["id"];

// 	$sql = "update user set pas= ? where id= ?";		//実行するsql文を入力
	
// 	$lock = "select * from user where id= ?";		//実行対象の行のselect文を入力
// 	$result = db_transaction($sql,$lock,$arr);      //connect.phpにある関数

// 	if($result){
// 		echo "成功";
// 	}

// 	if(isset($_SESSION["db_errmsg"])){
// 		echo $_SESSION["db_errmsg"];
// 	}
// }
?>
<form action="<?= $_SERVER["SCRIPT_NAME"] ?>" method="post">
	<div>ID<br>
		<input type="text" name="id" size="8"><br>
		パスワード<br>
		<input type="password" name="pas" size="8"><br>
		<br>
		<input class="btn" type="submit" name="btn" value="登録">
	</div>
</form>