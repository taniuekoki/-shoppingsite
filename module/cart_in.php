<?php
// 1-26 13:36 谷上
//02-01 16:05 谷上　sale_limit 追加
session_start();

header("Cache-Control: no-store");

$errmsg = "";
$item = ["item_code","item_name","price","sale_flag","sale_ratio","sale_price",
		"campaign_name","seller_date","thumbnail","customer_code","sale_limit"];
$cart=array();
foreach ($item as $val) {
	$cart[$val] = $_POST[$val];
}

//すでにカートに入っているか判定

$flag = 0;	//１で残っているときがあるため０でリセット

if(isset($_SESSION["cart"]) && count($_SESSION["cart"])){
	$sesstion = $_SESSION["cart"];
	foreach ($sesstion as $val) {
		if(in_array($cart["item_code"], $val)){
 			$errmsg = "すでにカートに入っています";
 			$flag = 1;	//すでにカートに入ってますフラグ
		}
	}
	if(!$flag){	//フラグがたっていなければ
		$_SESSION["cart"][]=$cart;
	}
}else{	
	//セッションがなければ
	$_SESSION["cart"][]=$cart;
}

/*----セッションカート数が１１以上なら１０に----*/
if (count($_SESSION["cart"]) > 10) {
	$cart_count = 10;
}else{
	$cart_count = count($_SESSION["cart"]);
}
/*---------------------------------------------*/

	
echo json_encode(array("cart_count"=>$cart_count,"errmsg"=>$errmsg,JSON_UNESCAPED_UNICODE));

/* 例：$_SESSION["cart"][0]["item_code"]=>10004
 * 						   ["item_name"]=>"犬"
 * 						   ["price"]=>200
 * 								:
 * 								:
 * 					    
 * 						[1]["item_code"]=>10005
 * 						   ["item_name"]=>"町"
 * 						   ["price"]=>160
 * 								:
 * 								:
 */
?>
