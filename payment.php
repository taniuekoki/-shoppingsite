<?php
//2-1 谷上　メール機能追加
//2-1 15:55 谷上　セール限定個数処理追加
//2023-02-08 fujimoto 警告文チェック追加
//2023-02-08 谷上　すでに購入しているか確認追加

session_start();
include("./module/connect.php");

$total_sum = $errflag = $total = 0;
$mail_paid_item ="";//メール用購入商品文字列


/*ログインチェック*/
require_once './module/UserLogic.php';
$login_check = UserLogic::checkLogin();
if(!$login_check){
	header("Location:./login.php");
	exit();
}
$arr[] = $_SESSION['login_user']['user_code'];

/*-----カートに商品がない場合-----*/
if(!isset($_SESSION["cart"])) {
	//カートが空なのでcart.phpにリダイレクトする(不正な遷移)
	header("Location:./cart.php");
	exit();
}else{
	if (empty($_SESSION["cart"]) || !count($_SESSION["cart"])) {
		header("Location:./cart.php");
		exit();
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["completion_btn"])) {
		//支払い完了ボタン処理
		$cart_judgement = $_SESSION["cart"];
		foreach ($cart_judgement as $cart_item){
			/*-----------すでに購入しているか確認↓------------------*/
			$arr_history=array();
			$arr_history[]=$_SESSION['login_user']['user_code'];
			$sql="select item_code from k2g2_sales where customer_code = ?";
			$result=db_execution($sql,$arr_history);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			
			foreach ($rows as $val){
				if($val["item_code"] == $cart_item["item_code"]){
					$cart_item["errmsg"][] = "この商品は購入済みです。";
					$errflag = 1;
				}
			}
			/*----------すでに購入しているか確認↑----------------*/
			
			$arr = array();
			$arr[] = $cart_item["item_code"];
			$sql = "select * from k2g2_item where item_code = ?";
			$result = db_execution($sql, $arr);
			$row = $result->fetch();
			
			if(!$row["sell_enable"] || $row["deleted_item"]){
				//商品が非表示または削除されていたら
				$cart_item["errmsg"][] = "この商品は購入できなくなりました";
				$errflag = 1;
			}else{
				if($cart_item["seller_date"] != $row["seller_date"]){
					//出品日時が変更されていたら（内容が編集されている場合）
					$cart_item["errmsg"][] = "この商品情報が変更されています";
					$errflag = 1;
				}
				
				/*-------------セール情報が変更されてないか確認----------------*/
				
				if($cart_item["sale_flag"]){	//カートに入っている商品がセール対象の場合
					if (!$row["sale_flag"]) {	//今もセールしているか確認
						//今はセール対象外だった場合
						$cart_item["errmsg"][] = "この商品の価格が変更されています";
						$errflag = 1;
					}else{
						$arr=array();
						$sql = "select * from k2g2_discount";
						$result = db_execution($sql, $arr);
						$row_sale = $result->fetch();
						if($cart_item["sale_ratio"] != $row_sale["sale_ratio"]){
							//割引率が変わっている場合
							$cart_item["errmsg"][] = "この商品の価格が変更されています";
							$errflag = 1;
						}
					}
				}
				/*-----------------セール情報確認ここまで---------------------*/
			}
			$cart[] = $cart_item;
		}
		if(!$errflag){	//カートに入っている商品に問題がなければ
			$db->beginTransaction();	//トランザクション開始
			$tran_err = false;	//トランザクション中にbreakしたか？
			foreach ($cart_judgement as $cart_item){
				//売上管理のレコードを作成
				$arr = array();
				$arr[] = $cart_item["item_code"];
				$arr[] = $cart_item["item_name"];
				if($cart_item["sale_price"]){
					$arr[] = $cart_item["sale_price"];
					$price = $cart_item["sale_price"];
				}else{
					$arr[] = $cart_item["price"];
					$price = $cart_item["sale_price"];
				}
				//購入者コードを入れる
				$arr[] = $_SESSION['login_user']['user_code'];
				$sql = "insert into k2g2_sales (item_code,item_name,sold_price,customer_code,sold_date) value (?,?,?,?,now())";
				$result = db_transaction($sql,$arr);
				if(!$result){
					break;
				}
				
				//商品管理の売上数を＋１
				$arr = array();
				$arr[] = $cart_item["item_code"];
				$sql = "select sold_count from k2g2_item where item_code = ? for update";
				$result = db_transaction($sql,$arr);
				if(!$result){
					$tran_err = true;
					break;
				}
				
				$sold_count=$result->fetch();
				
				//売上数を＋１
				$sold_count[0]++;
				
				//売上数を配列の先頭にいれる
				array_unshift($arr,$sold_count[0]);
				
				//DBの売上数を上書きする
				$sql="update k2g2_item set sold_count = ? where item_code = ?";
				$result = db_transaction($sql,$arr);
				if(!$result){
					$tran_err = true;
					break;
				}
				
				//出品者の会員管理の所持売上金に加算
				$arr = array();
				$arr[] = $cart_item["customer_code"];
				$sql = "select own_value from k2g2_customer where customer_code = ?";
				$result = db_transaction($sql, $arr);
				if(!$result){
					$tran_err = true;
					break;
				}
				
				$own_value=$result->fetch();
				
				//通常価格
				$own_value[0] += $cart_item["price"];
				
				//所持売上金を配列の先頭にいれる
				array_unshift($arr,$own_value[0]);
				
				//DBの所持売上金を上書きする
				$sql="update k2g2_customer set own_value = ? where customer_code = ?";
				$result = db_transaction($sql, $arr);
				if(!$result){
					$tran_err = true;
					break;
				}
				
				//セール商品であれば限定個数を減らす
				if ($cart_item["sale_flag"]) {
					//現在のセール限定個数を取得
					$sale_limit = $cart_item["sale_limit"];
					if($sale_limit > 0){
						//セール限定個数を-1する
						$sale_limit -= 1;
						//セール限定個数上書き
						$arr = array();
						$arr[] = $sale_limit;
						$sql="update k2g2_item set sale_limit = ? ";
						
						if ($sale_limit == 0) {
							//セール限定個数が完売したらセールフラグを下す
							$sql .= ",sale_flag = false ";
						}
						$sql .="where item_code = ?";
						
						$arr[] = $cart_item["item_code"];
						$result = db_transaction($sql, $arr);
					}
				}
				if(!$result){
					$tran_err = true;
					break;
				}
				
				$total += $price;
				$mail_paid_item .= $cart_item["item_name"]."：".$price."円\n";
			}
			
			//*******************商品forループここまで********************//
			
			/**購入完了メールを送信**/
			
			//エンコード
			mb_language("Japanese");
			mb_internal_encoding("UTF-8");
			
			//ユーザーメールアドレス平文
			$to = $_SESSION['login_user']['user_email'];
			
			//メールタイトル
			$subject = "【バズルシャシンヤサン】購入完了メール";
			
			//本文
			$message = $_SESSION['login_user']['user_name']."さんご購入ありがとうございます。\n
購入日：".date('Y年n月j日 G時i分')."\n
[商品名]：[金額]
".$mail_paid_item."
支払方法：".$_POST["siharai"]."\n
合計支払金額：".$total."円\n
マイページの購入履歴からダウンロードできます。\n\n
バズルシャシンヤサン\n
http://websystem.rulez.jp/22/web22g2/purchasehistory.php\n
\n
＊＊＊＊注意事項＊＊＊＊\n
本メールは実習課題として作成されており、本メールの内容はすべて架空のデモですので、一切の金銭のやりとり等は発生しておりません。よって、本メールを領収書等に利用することはできません。\n
＊＊＊＊＊＊＊＊＊＊＊＊\n
◆このメール内容に覚えが無い場合は、申し訳ございませんが本メールの削除をお願いいたします。
";
			
			//サイトからの送信元メールアドレス
			$headers = "From: " .mb_encode_mimeheader("バズルシャシンヤサン") ."<web22g2@websystem.rulez.jp>";
			
			//メール送信実行
			$result2 = mb_send_mail($to, $subject, $message,$headers);
			
			if(!$result2){
				$_SESSION['msg'] = "メール送信に失敗しました";
				$tran_err = 1;
			}
			if(!$tran_err){
				//カートのセッション削除
				unset($_SESSION["cart"]);
				$db->commit();		//コミット
				
				$_SESSION["payout"] = true;	//2023-02-08藤本追加
				header("Location:./complete.php");
				exit();
			}else{
				$errflag = 1;
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
  <title>2班サイト</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/payment.css">
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/payment.js"></script>
</head>

<body>
	<div class="innerWrap">
	  <header class="header">
	      <?php include "header.php" ?>
	  </header>
	</div>
	<main>
		<div class="innerWrap">
		    <h1 class="hh">支払方法</h1>
		    
<!-- ***********ここから商品*********** -->

<?php
//カート商品一覧表示
if ($errflag) {
	?><div class="errmsg">購入処理中にエラーが発生しました。</div><?php 
}else{
	$cart = $_SESSION["cart"];
}
foreach ($cart as $cart_item){
	?>
	
	<table>
		<tr>
			
			<th>
				<div class="syouimg">
					<img src="./images/thumbnails/owners/<?= $cart_item["customer_code"] ?>/<?= $cart_item["thumbnail"] ?>" alt=""><br><br>
				</div>
			</th>
			
			<td>
				<div class="sho">
					<div class="clm">
						<p><b>商品名</b>：<?= $cart_item["item_name"] ?></p>
						<?php 
							//セールフラグがたっていれば
							if($cart_item["sale_flag"]){
							//セール価格を表示する
						?>
						<div class="flex">
							<p><b>割引後価格</b>：<span class="red">\<?= $cart_item["sale_price"] ?></span></p>
						</div>
						
						<?php
							//合計金計算
							$total_sum += $cart_item["sale_price"];
							}else{
							//定価表示
						?>
						
						<p><b>価格</b>：\<?= $cart_item["price"] ?></p>
						
						<?php
							//合計金額計算
							$total_sum += $cart_item["price"];
							}
						?>
						<p></p>
					</div>
					
					<!-- 商品情報が変更された等、エラーメッセージ表示 -->
					<p class="errmsg">
						<?php 
							if(isset($cart_item["errmsg"])){
								foreach ($cart_item["errmsg"] as $val) {
									echo $val."<br>";
								}
							}
						?>
					</p>
				</div>
			</td>
		</tr>
	</table>
	
		<div class="line"></div><br>
		
		<!-- ***********ここまで商品*********** -->
		<?php }?>
			
			
			<div class="migi">
				<nav class="pay">
					<p class="goukei">合計金額 \<?=$total_sum ?></p>
					<?php 
					//カート画面と消費税が違う
					$tax = $total_sum - ($total_sum / 1.1);
					$tax = floor($tax);
					?>
					<p>（内税\<?=$tax?>）</p>
					<form action="#" method="POST" name="siharai" id="payoutform">
					<p>
						<label><input type="radio" name="siharai" value="電子決済" checked="checked">電子決済</label><br>
						<label><input type="radio" name="siharai" value="クレジットカード">クレジットカード</label>
					</p>
						<p class="attention">このシステムは実習課題であり、実際に購入を行えるサイトではなく、金銭のやりとりも発生することはありません。</p>
						<input type="checkbox" name="paychkbox" id="paychkbox">
						<label for="paychkbox">同意する</label>
						<input id="payoutbtn" type="submit" name="completion_btn" value="支払確定">
					</form> 
				</nav>
			</div>
		</div>
	</main>
		<div class="innerWrap">
			<footer>
			<?php include 'footer.php';?>
			</footer>
		</div>
</body>
</html>