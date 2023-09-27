<?php
// 02-01 14:50 谷上	購入履歴がない時処理
//0206 15:30 前更41行目SQL文を「select sold_date,item_name,sold_price,item_code from k2g2_sales where customer_code = ? and deleted_item = 0 order by sold_date desc;」から
//「SELECT sold_date,item_name,sold_price,item_code FROM k2g2_sales JOIN k2g2_item ON k2g2_sales.item_code = k2g2_item.item_code WHERE customer_code = ? AND deleted_item = 0 AND k2g2_item.deleted_flag = 0 ORDER BY sold_date DESC;」に変更
//				 94行目SQL文に「and deleted_item = 0」追加
session_start();
/*ログインチェック*/
require_once './module/UserLogic.php';
$login_check = UserLogic::checkLogin();
if(!$login_check){
	header("Location:./login.php");
	exit();
}
require_once("./module/connect.php");
$arr[] = $_SESSION['login_user']['user_code'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST["download"])){		//ダウンロードボタンが押された時の処理
		//本当に購入しているか確認
		//ダウンロードする画像のパスを取得
		$image_path = pathinfo($_POST["download_image"]);
		//拡張子以外を取得
		$arr_item[] = $image_path["filename"];
		//クリックされた商品の購入者を取得
		$sql="select customer_code from k2g2_sales where item_code = ?";
		$result=db_execution($sql,$arr_item);
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $val){
			if($val["customer_code"] == $arr[0]){
				//商品の購入者にログイン中のカスタマ連番があれば
				//ダウンロード処理
				$image_name = $_POST["download_image"];
				$owner = $_POST["download_image_owner"];
				image_download($download_path,$owner,$image_name);
			}
		}
	}
}

//ログイン中のカスタマ連番で購入履歴を照会(購入日付で降順)
$sql=" SELECT sold_date, k2g2_item.item_name, sold_price, k2g2_item.item_code FROM k2g2_sales JOIN k2g2_item ON k2g2_sales.item_code = k2g2_item.item_code WHERE k2g2_sales.customer_code = ? AND deleted_item = 0 AND deleted_item = 0 ORDER BY sold_date DESC; ";

$result=db_execution($sql,$arr);


//自動ダウンロードメソッド
function image_download($download_path,$owner,$image_name) {
	//ファイルはWebからアクセス出来ないけどPHPからアクセスできるところに置く
	$file = $download_path.'/owners/'.$owner.'/'.$image_name;
	//ファイル名を取得
	
	$filename = pathinfo($file)['basename'];
	//ファイルサイズを取得
	$len = filesize($file);
	
	//ファイル情報をHeaderで出力
	header("Content-Type: application/image");
	header("Content-Length: {$len}");
	header("Content-Disposition: inline; filename={$filename}");
	
	//ファイルを読み込んで出力
	readfile($file);
	
	exit();
}
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>購入履歴</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/purchasehistory.css">
  <script src="js/jquery-2.1.4.min.js"></script>
<!--   <script src="js/purchasehistory.js"></script> -->
</head>

<body>
	<div class="innerWrap">
		<header class="header">
			<?php include "header.php" ?>
		</header>
	</div>
	
	<main>
		<div class="innerWrap">
			<h1>購入履歴</h1>
			
			<?php 
				//商品リストループ開始**********************************************************
				while ($row = $result->fetch()) {
				
					$arr_item = array();
					$arr_item[] = $row["item_code"];
					$sql_item = "select * from k2g2_item where item_code = ? && deleted_item = 0";
					
					$result_item = db_execution($sql_item,$arr_item);
					
					$row_item = $result_item->fetch();		
			?>
			
			
			<table>
				<tr>
					<th>
						<div class="syouimg">
							<img src="./images/thumbnails/owners/<?=$row_item["customer_code"]?>/<?=$row_item["thumbnail"]?>" alt="商品画像"><br><br>
						</div>
					</th>
					
					<td class = "td1">
						<div class="sho">
							<div class="day">購入日：<?=$row["sold_date"] ?></div>
							<div class="clm">
								<p>商品名：<?=$row["item_name"] ?></p>
								<p>購入価格：￥<?=$row["sold_price"] ?></p>
							</div>
						</div>  
					</td>
					<td class = "td2">
						<div class="sho2">
							<form action="item.php" method="get">
								<input type="hidden" name="item_code" value="<?=$row["item_code"] ?>">
								<button class="bt" id="item_detail" type="submit" >商品詳細</button>
							</form><br>
							<form action="<?= $_SERVER["SCRIPT_NAME"] ?>" method="post">
								<input type="hidden" name="download_image_owner" value="<?=$row_item["customer_code"] ?>">
								<input type="hidden" name="download_image" value="<?=$row_item["image"] ?>">
								<input class="bt"  type="submit" name="download" value="ダウンロード">
							</form>
						</div>
							</td>
				</tr>
			</table>
					<div class="line"></div><br>
					<?php }
					
					//商品リストループ終了*********************************************************?>
				
				<?php 
					if($result->rowCount() == 0){
				?>
				<p id="no_item">購入履歴がありません。</p> 
				
				<?php 
					}
				?>
			
			</div>
	</main>
		<div class="innerWrap">
			<footer>
			<?php include 'footer.php';?>
		</footer>
		</div>
</body>
<?php //}?>
</html>