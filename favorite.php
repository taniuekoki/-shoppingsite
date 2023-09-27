<?php
//喜多がやってますーーーー
//2023-02-03 15:26 sql文変更（非公開・削除対策）谷上
//2023-03-07 11:00 fujimoto titleタグ直し 円対応
//2023-03-07 12:00 谷上 取り消し線iphoneで反映されない対応 

session_start();

$s_flg = 1;
$sort = "desc";
// ログインチェック
if(!isset($_SESSION['login_user']['user_code'])){
	//ログインしていなかったら
	$s_flg = 0;
}else{
	
	include("./module/connect.php");
	
	$fav_err = "";
	if(isset($_SESSION["fav_delete_err"]) && !empty($_SESSION["fav_delete_err"])){
		$fav_err = $_SESSION["fav_delete_err"];
		unset($_SESSION["fav_delete_err"]);
	}
	
	//ログイン中のカスタマ連番を入力
	$customer_code=$_SESSION['login_user']['user_code'];
	
	
	//ログイン中のカスタマ連番でお気に入りを照会
	$arr[]=$customer_code;
	$sql="select fav_item from k2g2_customer where customer_code = ? ";
	
	$result_fav = db_execution($sql,$arr);
	$fav = $result_fav->fetch();
	
	//カンマ区切りを配列に入れる
	$item_arr = explode(',', $fav["fav_item"]);
	
	//並び替え
	if(isset($_GET["sort"]) && $_GET["sort"] == "古い順"){
		$sort = "asc";
	}
	if(isset($_GET["sort"]) && $_GET["sort"] == "新着順"){
		$sort = "desc";
	}
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST["bt"]) && $_POST["bt"] == "お気に入り削除"){		//お気に入り削除が押されたら
			$delete_item_code = $_POST["delete_item_code"];
			
			//一応コピー
			$deleted_arr = $item_arr;
			
			//array_search(検索する値, 検索対象の配列, 型の比較を行うか[省略可])
			$key = array_search($delete_item_code, $deleted_arr);
			
			
			//配列を再構築（配列の中から削除ボタン押された商品コードを抜く）
			//削除するキーがあれば配列から消す
			if(is_numeric($key)){
				unset($deleted_arr[$key]);
				//配列を文字列に変換
				$new_fav = implode(",", $deleted_arr);
				
				
				//アップデート文でfav_itemを書き換える
				array_unshift($arr,$new_fav);
				$sql2= "UPDATE k2g2_customer SET fav_item = ? where customer_code = ?";
				
				$result_del = db_execution($sql2,$arr);
				
				if($result_del){
					//アップデート成功したら$deleted_arrを画面表示にまわす
					$item_arr = $deleted_arr;
				}else{
					//失敗してたら、元の$arrを画面表示
					$_SESSION["fav_delete_err"] = "DBエラー：削除に失敗しました。";
					header("Location:./favorite.php");
					exit();
				}
			}
		}
	}
}

?>

<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Picstock</title>
	<link rel="stylesheet" href="css/reset.css">
	<link rel="stylesheet" href="css/purchasehistory.css">
	<link rel="stylesheet" href="css/favorite.css">
	<script src="js/jquery-2.1.4.min.js"></script>
	<script src="js/favorite_cart_ajax.js"></script>
	<script src="js/search.js"></script>
</head>

<body>
	<div class="innerWrap">
		<header class="header">
			<?php include "header.php" ?>
		</header>
	</div>
	
	<main>
		<div class="innerWrap">
			<h1>お気に入り</h1>
			<div class="selectwrap">
				<form name="" action='' method="get" id="sortform">
					<select class="sort" name="sort">
						<option value="新着順" <?php if($sort=="desc") echo " selected" ;?>>新着順</option>
						<option value="古い順" <?php if($sort=="asc") echo " selected" ;?>>古い順</option>
					</select>
				</form>
			</div>
			
			<?php 
				if(!$s_flg){
					//ログインしていなかったら
					echo '<p class="false"><br><br><br><br>ログインしてください。</p>';
				}else{
					if(empty($item_arr) || empty($fav[0])){
					//お気に入りが０だったら
					echo '<p class="false"><br><br><br><br>現在お気に入りはありません。</p>';
				}else{
					echo $fav_err;
					
					//商品リストループ開始**********************************************************
					
					//古い順だったら配列を入れ替える
					if($sort == "asc"){
						//入れ替える
						$item_arr = array_reverse( $item_arr);
					}
					if($sort == "desc"){
						$item_arr;
					}
					
					foreach ($item_arr as $val){
						$arr_item = array();
						$arr_item[] = $val;
						
						$sql_item = "select * from k2g2_item where item_code = ?  && sell_enable = 1 && deleted_item = 0;";
						
						$result_item = db_execution($sql_item,$arr_item);
						
						$row_item = $result_item->fetch();
						
						//商品情報が何もない・非公開・削除されているとき
						if ($result_item->rowCount() == 0) {
							continue;
						}
						
						//セール判定
						if($row_item["sale_flag"]){
							$arr_sale=array();
							$sql_sale = "select * from k2g2_discount";
							$result_sale = db_execution($sql_sale,$arr_sale);
							$row_sale = $result_sale->fetch();
							
							$sale_price = $row_item["price"] * (1-($row_sale["sale_ratio"]/100));
							
						}
					?>
					
					
					<table>
						<tr>
							<th>
								<div class="syouimg">
									<img src="./images/thumbnails/owners/<?=$row_item["customer_code"] ?>/<?=$row_item["thumbnail"]?>" alt="商品画像"><br><br>
								</div>
							</th>
							
							<td class = "td1">
								<div class="sho">
									<div class="clm">
										<p>商品名：<?=$row_item["item_name"] ?></p>
										
										<!--------------------------- 追加しました↓-------------------------->
										<p>価格：
										<?php
										if ($row_item["sale_flag"]) {
											?><span id="price" class="discount">&yen;<?=$row_item["price"] ?></span><?php
											echo "(".$row_sale["sale_ratio"]."%OFF!!)";
										}else{
											?><span id="price">&yen;<?=$row_item["price"] ?></span><?php
										}
										?>
										</p>
										<?php 
											if ($row_item["sale_flag"]) {
										?>
										<p>割引後価格：<span class="red">&yen;<?=$sale_price ?></span></p>
										<?php 
											}else{
												//セールしていなくてもセッションカートに保存したいので"0"を代入
												$row_sale["sale_ratio"]=$sale_price=$row_sale["campaign_name"]=$row_item["sale_limit"] = 0;
											}
										?>
										<!--------------------------- 追加しました↑-------------------------->
									</div>
								</div>
							</td>
							
							<td class= "td2">  
								<div class="sho2">
									<form name="k2" action='<?= $_SERVER["SCRIPT_NAME"]?>' method="post" onSubmit="return check()">
										<input type="hidden" name="delete_item_code" value="<?= $val ?>">
										<input class="bt radius20" type="submit" name="bt" value="お気に入り削除">
									</form><br>
									<a class="detail" href="./item.php?item_code=<?=$row_item["item_code"]?>">商品詳細</a><br><br>
									<button id="cart_button" class="cart_button_class radius20"  name="cart" value="<?=$row_item["item_code"]?>">カートに入れる</button><br>
									<br><div class="cart_msg" id="cart_in_success<?=$row_item["item_code"] ?>"></div>
									<br><div class="cart_errmsg" id="errmsg<?=$row_item["item_code"] ?>" style="color:red;"></div>
									<!------------- ajax用↓ ---------->
									<?php 
										/*---------購入履歴確認-----------*/
										$arr_history=array();
										$arr_history[]=$_SESSION['login_user']['user_code'];
										$sql="select item_code from k2g2_sales where customer_code = ?";
										$result=db_execution($sql,$arr_history);
										$rows = $result->fetchAll(PDO::FETCH_ASSOC);
										
										foreach ($rows as $val){
											if($val["item_code"] == $row_item["item_code"]){
												$history_flag=1;
												break;
											}else{
												$history_flag=0;
											}
										}
										/*---------購入履歴確認終了-----------*/
										$key = ["item_code","item_name","price","sale_flag",
										"seller_date","thumbnail","customer_code","sale_limit"];
										foreach ($key as $val) {
									?>
									<input class="<?=$row_item["item_code"]?>" type="hidden" name="<?=$val?>" value="<?=$row_item[$val]?>">
									<?php }  ?>
									
									<input class="<?=$row_item["item_code"]?>" type="hidden" name="history_flag" value="<?=$history_flag?>">
									<input class="<?=$row_item["item_code"]?>"type="hidden" name="sale_ratio" value="<?=$row_sale["sale_ratio"]?>">
									<input class="<?=$row_item["item_code"]?>"type="hidden" name="campaign_name" value="<?=$row_sale["campaign_name"]?>">
									<input class="<?=$row_item["item_code"]?>"type="hidden" name="sale_price" value="<?=$sale_price?>">
									<input class="check" type="hidden" value="<?=$row_item["item_code"]?>">
								</div>
							</td>
						</tr>
					</table>
				
				<div class="line"></div><br>
						<?php //商品リストループ終了*********************************************************
						}
					}
				}
			?>
		</div>
		</main>
	<div class="innerWrap">
		<footer>
			<?php include "footer.php"?>
		</footer>
	</div>
	
</body>
<script>

function check(){

	if(window.confirm('お気に入りから削除しますか？')){ // 確認ダイアログを表示

		return true; // 「OK」時は送信を実行

	}
	else{ // 「キャンセル」時の処理

		
		//location.reload();
		return false; // 送信を中止

	}

}

//並び順変更
$(function(){
	
	$(".sort").change(function(){
		$('#sortform').submit();
	});
});

</script>
</html>