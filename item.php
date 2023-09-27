<?php 
//1-31 15:10 谷上 
//2-01 10:22 taniue #ジャンル検索にタグも送る行追加
//02-02 10:00 fujimoto 
//2023-03-07 11:00 fujimoto titleタグ直し　円マーク？？
//2023-03-07 12:00 谷上 取り消し線iphoneで反映されない対応

/*---キャッシュ対策---*/
header("Cache-Control: no-store");	
/*キャッシュに保存しない（戻るボタン押してもキャッシュが残っていないのでサーバーにアクセスする）*/

session_start();

include("./module/connect.php");
include("./module/taglist.php");

/*-------------------変数宣言・初期値------------------------*/
$arr=array();
$sale_price="";
$history_flag= $item_code_missing_flag = 0;
/*-----------------変数宣言・初期値ここまで-------------------*/
//ログインチェック
$login_userdata = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : NULL;

/***************お気に入りチェック***************/
//2023-02-01 fujimoto
$fav_check = false; //お気に入りされてるか？初期値(false);

if(isset($login_userdata)&& isset($_GET["item_code"])){
	//お気に入りチェック開始
	$fav_item_code= $_GET["item_code"];
	$fav_arr = array();
	$fav_arr[] = $login_userdata['user_code'];
	$sql = "select * from k2g2_customer where customer_code = ?";
	$result = db_execution($sql,$fav_arr);
	
	if($result){
		$fav_data = $result->fetch();
		$fav_arr = explode(',', $fav_data["fav_item"]); //カンマ区切り文を配列に変換する
		//fav配列内に商品が入っているか？
		if(in_array($fav_item_code,$fav_arr)){
			$fav_check = true;
		}
	}
}
/***************お気に入りチェック終***************/



if (isset($_GET["item_code"])) {
	$arr[]= $_GET["item_code"];
}else{
	header("Location:./index.php");
	exit();
}


//購入履歴すでにあるか判定
if (isset($_SESSION['login_user']['user_code'])) {
	$arr_history=array();
	$arr_history[]=$_SESSION['login_user']['user_code'];
	$sql="select item_code from k2g2_sales where customer_code = ?";
	$result=db_execution($sql,$arr_history);
	$rows = $result->fetchAll(PDO::FETCH_ASSOC);
	
	foreach ($rows as $val){
		if($val["item_code"] == $arr[0]){
			$history_flag=1;
		}
	}
}

$sql="select * from k2g2_item where item_code = ?";
$result=db_execution($sql,$arr);

//アイテムコードが存在しなかったら
if($result->rowCount()){
	$row = $result->fetch();
	//セール判定
	if($row["sale_flag"]){
		$arr_sale=array();
		$sql_sale = "select * from k2g2_discount";
		$result_sale = db_execution($sql_sale,$arr_sale);
		$row_sale = $result_sale->fetch();
		
		$sale_price = $row["price"] * (1-($row_sale["sale_ratio"]/100));
		
	}
}else{
	$item_code_missing_flag = 1;
}
?>
<!doctype html>

<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Picstock</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/zoom.css">
  <link rel="stylesheet" href="css/item.css">
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/zoom.js" type="text/javascript"></script>
  <script src="js/item_cart_ajax.js"></script>
  <script src="js/item_fav_ajax.js"></script>
</head>
<body>
  <div class="innerWrap">
  <header class="header">
    
      <?php include "header.php" ?>
  </header>
<?php 

if($item_code_missing_flag || !$row["sell_enable"] || $row["deleted_item"]){
	echo "この商品は非公開または、削除された商品です。";
}else{
	

?>
  <main>
<?php if($history_flag) {
	?><p id="history">購入済み商品です</p><?php
}?>
    <div class="syouhin">
      <div class="syouimg">
        <img src="./images/thumbnails/owners/<?=$row["customer_code"];?>/<?=$row["thumbnail"]?>" alt="商品画像" alt="商品サムネイル" data-action="zoom"><br><br>
        
      </div>
      
      <div class="setumei">
        <br>
        <p class="toi">商品名：<?=$row["item_name"] ?></p>	
        
        <!------- カテゴリ・タグ ------->		
        <div class="tagu">
        
        	<div class="flex">
        		タグ：
	        	<form action="search.php" method="GET" name="genre" >
	            <button class="link" type="submit" name="genre" value="<?=$row["item_genre"] ?>">
	            #<?=$genre_list[$row['item_genre']] ?>
	            </button>
	            <input type="hidden" name="tag" value="all">
	            </form>
	            >
	            <form action="search.php" method="GET" name="tag" >
	            <input type="hidden" name="genre" value="<?=$row["item_genre"] ?>">
	            <button class="link" type="submit" name="tag" value="<?=$row["tag"] ?>">
	            #<?=$tag_list[$row['tag']] ?>
	            </button>
	            </form><br><br>
            </div>
            
            <p>価格：
						
            <?php
            if ($row["sale_flag"]) {
							?><span id="price" class="discount">&yen;<?=$row["price"] ?></span><?php
            	echo "(".$row_sale["sale_ratio"]."%OFF!!)";
            }else{
							?><span id="price">&yen;<?=$row["price"] ?></span><?php
						}
            ?>
            </p><br>
            <?php 
            if ($row["sale_flag"]) {?>
            <p>割引後価格：<span class="red">&yen;<?=$sale_price ?></span></p><br>
            
            <div class="sale"><br>
	            <p class="red"><?=$row_sale["campaign_name"] ?>中！！</p> <br> 	
	            <p class="red">セール限定数残り<?=$row["sale_limit"] ?>個</p><br>
            </div>
            
            <?php 
            }else{
            	//セールしていなくてもセッションカートに保存するため"0"を代入
            	$row_sale["sale_ratio"]=$sale_price=$row_sale["campaign_name"]=$row["sale_limit"] = 0;
            }
          	?>
        </div>
        
         
        <div class="like_on">
            <form action method="POST" name="like">
            <input id="like" type="button" name="like" value="お気に入りに追加"> 
            </form>
        </div>
        <div class="like_off">
            <form action method="POST" name="notlike">
            <input id="notlike" type="button" name="notlike" value="お気に入りから削除"> 
            </form>
        </div>
       <div id="like_in"></div>
       			<input type="hidden" name="login_userdata" value="<?php if(isset($login_userdata)) echo $login_userdata["user_code"] ?>">
       <?php if(isset($login_userdata)){ ?>
				<input type="hidden" name="fav_check" value="<?=$fav_check? "1" : "0" ?>">
				<input type="hidden" name="fav_item_code" value="<?=$row["item_code"]?>">
       <?php } ?>
        <br>    
        <form action method="POST" name="cart" >
         	<input id="cart_button" type="button" name="cart" value="カートに入れる">
        </form><br>
        <div id="cart_in_success" class="cart_msg"></div>
        <div id="errmsg" style="color:red;" class="cart_msg"></div>
        
        
        <!------------- ajax用↓ ---------->
		  <?php 
		  $key = ["item_code","item_name","price","sale_flag",
		  		"seller_date","thumbnail","customer_code","sale_limit"];
		  foreach ($key as $val) {
		  	?><input type="hidden" name="<?=$val?>" value="<?=$row[$val]?>"><?php 
		  }
		  ?>
		  
		  <input type="hidden" name="history_flag" value="<?=$history_flag?>">
		  <input type="hidden" name="sale_ratio" value="<?=$row_sale["sale_ratio"]?>">
		  <input type="hidden" name="campaign_name" value="<?=$row_sale["campaign_name"]?>">
		  <input type="hidden" name="sale_price" value="<?=$sale_price?>">
      </div>

			
			</div>
  </main>
<?php }?>

	<div class="innerWrap">
		<footer>
		<?php include 'footer.php';?>
		</footer>
	</div>
</body>
</html>