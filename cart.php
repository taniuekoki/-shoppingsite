<?php 
//最終更新日 2023-01-31-15:00 谷上

session_start();
$shoukei = 0;
$zei = 0;
$sale = 0;
if(!isset($_SESSION["cart"])){
	$_SESSION["cart"] = array();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	/*---------------カートの削除処理------------*/
	if (in_array("カートから削除", $_POST, TRUE)) {
		$delbtn = array_search("カートから削除", $_POST, TRUE);
		list(, $delno) = explode("_", $delbtn);
		unset($_SESSION["cart"][$delno]);
	}
	/*---------カートの削除処理ここまで----------*/
	
	header("Location:./cart.php");
	exit();
}
?>


<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>2班サイト</title>
  
  <link rel="stylesheet" href="css/reset.css">
<!--   <link rel="stylesheet" href="css/cart2.css"> -->
  <link rel="stylesheet" href="css/cart.css">
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/payment.js"></script>
  
</head>
<script>
$(function(){
	let payment_flag = 0;
<?php 
if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])){?>
	payment_flag = 1;
<?php 
}else{?>
	payment_flag = 0;
<?php }?>

	if(payment_flag){
		$("#payment").show();
	}else{
		$("#payment").hide();
	}

});
</script>
<body>
	<div class="innerWrap">
	  <header class="header">
	  <?php include "header.php" ?>
	  </header>
	</div>
	<main>
		<div class="innerWrap">
		<h1>ショッピングカート</h1>
		<?php
		if (empty($_SESSION["cart"])) {
    	echo "<strong> カートの中身は空です。 </strong>";
    }
    
		if(isset($_SESSION["cart"])){
    	foreach ($_SESSION["cart"] as $key => $val) { 
    ?>
    <table>
     	<tr>
    		<th>
      		<img src="./images/thumbnails/owners/<?= $val['customer_code'] ?>/<?= $val['thumbnail'] ?>" alt="商品サムネイル">
        </th>
        <td>
        <?php 
					if ($val['campaign_name']) {	
				?>
				<div>
				<?php
           echo "<p><b>商品名</b>：". $val['item_name']. "　　　 　　</p>";
    	        echo "<p><b>価格</b>：<span class='discount'>\\". $val['price']. "</span>(".$val["sale_ratio"]."%OFF)</p>";    
							         ?>
                    	</div>
                    	<div>
                    <?php 
                    echo "<p><b>割引後価格</b>：<font color=red>\\". $val['sale_price']. "</font></p>"
                     ?>
                     	</div>
                        
                     <?php 	
                     	$sale += ($val['price'] - $val['sale_price']);
        	    		
                        
				    } else {
				     ?>
				     <div>
				     <?php
					    echo "<p><b>商品名</b>：". $val['item_name']. "</p>";
					    echo "<p><b>価格</b>：\\". $val['price']. "</p>";
					 ?>  
					 </div>
					 <?php 
					}
					
					$shoukei += $val['price'];
					?>
					
					<div class="flex">
						<div class=btn1>
                           	<form action="item.php" method="get">
                        	    <button class="bt" type="submit" name="item_code" value="<?= $val['item_code'] ?>">商品詳細</button>
            				</form>
                    	</div>	
                       	<div class=btn2>
                       		<form action=" <?= $_SERVER["SCRIPT_NAME"] ?>" method="post">
                    	   	    <button class="bt" type="submit" name="<?= "delbtn_" . $key ?>" value="カートから削除">カートから削除</button>
            				</form>
        				</div>
    				</div>
					
              	</td>
              	
              
              </tr>
              </table>
              
              
              
			<div class="line"></div>
			<?php 	
			}
            }
            $shokei = $shoukei - $sale;
			$zei = $shokei - ($shokei / 1.1);
        	$zei = floor($zei);
			?>
			<div class="migi">
				<nav class="pay">
					<p><b>小計</b>：\<?= $shoukei ?></p>
					<p class="discount_val"><b>割引額</b>：\<?= $sale ?></p>
					<div class="sline"></div>
					<p class="goukei">合計金額 \<?= $shoukei - $sale ?></p>
					<p>（内税：\<?= $zei ?>）</p>
					<b>
					<button id="payment" onclick="location='payment.php'">レジへ進む</button><br>
					<div id="keep_shopping">
					<?php
					if (!empty($_SERVER['HTTP_REFERER'])) {
						if (($_SERVER["REQUEST_METHOD"] != "POST" && (strpos($_SERVER['HTTP_REFERER'],"cart.php")=== false) )&& strpos($_SERVER['HTTP_REFERER'],"payment.php")=== false ) {
							//GETのとき
							//ホスト名取得
							$h = $_SERVER['HTTP_HOST'];
							if ((strpos($_SERVER['HTTP_REFERER'],$h) !== false)) {
								$_SESSION["url"] = $_SERVER['HTTP_REFERER'];
								?><a href="<?=$_SERVER['HTTP_REFERER']?>"><button class="back" type="button" name="payment">お買い物を続ける</button></a>
								<?php 
							}
						
						}else{
							//POST&レジから来たとき
								?><a href="<?=$_SESSION['url']?>"><button class="back" type="button" name="payment" >お買い物を続ける</button></a>
								<?php 
						}
					}else{
						?><a href="index.php"><button class="back" type="button" name="payment">TOP画面へ</button></a>
								<?php 
					}
					?>
					</div>
					</b>
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