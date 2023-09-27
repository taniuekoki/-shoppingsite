<?php 

//2023-03-01 fujimoto 初期実装

session_start();

chdir("../"); //カレントディレクトリの変更
require_once "./module/connect.php";


if ($_SERVER["REQUEST_METHOD"] == "POST"){
 if(isset($_POST["cam_execute"])){
	$cam_execute_flg = true;
 }

	$sql = "select * from k2g2_item where deleted_item = 0 and sell_enable = 1 ";
	$arr = array();
	//全品取得の場合は以下のifは通らない。
	
	
	//現在のセール対象商品取得
	if(isset($_POST["current_sale"])){
		$sql .= "and sale_flag = 1";
	}
	
	//値段下限と上限 絞り込みONの場合
	if(isset($_POST["price_low"]) && isset($_POST["price_high"])){
		$sql .= "and price between ? and ? ";
		$arr[] = $_POST["price_low"];
		$arr[] = $_POST["price_high"];
	}
	
	//ジャンル 絞り込みONの場合
	if(isset($_POST["item_genre"]) && isset($_POST["tag"])){
		if($_POST["item_genre"] != "all"){
			$sql .= "and item_genre = ? ";
			$arr[] = $_POST["item_genre"];
		}
		if($_POST["tag"] != "all"){
			$sql .= "and tag = ? ";
			$arr[] = $_POST["tag"];
		}
	}
	
	//campaign.phpで使う用
	$_SESSION["sql"] = $sql;

	// echo $sql;
	$result = db_execution($sql, $arr);
	
	$rows = $result->fetchAll();
	
	$data = array();
	// $data[] = $sql;
	foreach ($rows as $row){
	// 	var_dump($row);
		$data[] = $row;
	}
	
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	


}













