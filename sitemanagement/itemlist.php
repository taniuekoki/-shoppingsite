<?php
//maesara 02/28 完成、Linux環境テスト中
//fujimoto 03/08 12:00 デザイン修正
//fujimoto 03/09 9:50 セッションエラー修正


chdir("../"); //カレントディレクトリの変更 
require_once './module/UserLogic.php';
require_once './module/connect.php';
	//DB接続の記述がしてあり
$dbh = $db;
require_once './module/taglist.php';
	
session_start();

//ログインチェック
if(!isset($_SESSION["admin_user"]["user_id"])){
	header("Location:./managerlogin.php");
	exit();
}
	
$sql = 'select * from k2g2_item where 1=1';

$item_name = '';
$item_code = '';
$A_price = 0;
$B_price = 0;
$genre = '';
$tag = '';
$chk1 = "";
$chk2 = "";
$chk3 = "";
$chk4 = "";

$A_price = 0;
$B_price = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	//中断してきた場合のフォーム復元処理
	if(isset($_POST["resume"])){

		if(!isset($_SESSION["itemlist_resume"])){
			header("Location:./itemlist.php");
			exit();
		}

		//POST内容を復元
		$_POST = $_SESSION["itemlist_resume"];
	}else{
		//POST内容をセッションに保存
		$_SESSION["itemlist_resume"] = $_POST;
	}



		$item_name = $_POST['item_name'];
		$item_code = $_POST['item_code'];
		$A_price = $_POST['A_price'];
		$B_price = $_POST['B_price'];
		$genre = $_POST['genre'];
		$tag = $_POST['tag'];
	//A<Bにする
		if($A_price > $B_price){
			$C_price = $A_price;
			$A_price = $B_price;
			$B_price = $C_price;
		}

		if(isset($_POST['chk1'])){
			$chk1 = $_POST['chk1'];
		}
		if(isset($_POST['chk2'])){
			$chk2 = $_POST['chk2'];
		}
		if(isset($_POST['chk3'])){
			$chk3 = $_POST['chk3'];
		}
		if(isset($_POST['chk4'])){
			$chk4 = $_POST['chk4'];
		}
}

$form_genre = "all";
if(isset($_POST["genre"]) && !empty($_POST["genre"])){
	$form_genre = $_POST["genre"];
}

$form_tag = "alltag";
if(isset($_POST["tag"]) && !empty($_POST["tag"])){
	$form_tag = $_POST["tag"];
}
?>

<!DOCTYPE html>
<html lang ="ja">
 <head>
    <meta charset="utf-8">
	<link rel="stylesheet" href="./css/itemlist.css">
	<script>const form_select = "genre" </script>
	<script>const form_tag = "<?= $form_tag ?>"</script>
	<script>const form_genre = "<?= $form_genre ?>"</script>
	
	<script src="./js/jquery-2.1.4.min.js"></script>
	<script src="./js/header.js"></script>
	<script src="./js/zoom.js"></script>

  <title>Picstock - Management</title>
 </head>
 <body>
	<main>
	<div class="main">
	<a href="./managermenu.php" id="logo" ><img src="../images/top1.png"></a>
	
	<h1>商品管理画面</h1>

	<div><button class="bigbtn"><a href="permission.php">商品を新規登録する</a></button></div> 
	<form action="" method="post" id="search_form">
		<div class="kasen"><label><input type="checkbox" name="chk1" value="chk1" <?php  if($chk1) echo "checked" ;?>>検索に含む</label><span class="zyoubu">商品ID</span><input type="text" name="item_code" value="<?php echo $item_code; ?>" size="8"></div>
		<?php  if($chk1 && !empty($item_code)) {
					$sql.= " and item_code =".$item_code ;
				}
//				else{$sql.= ";";}
				?>
		<div class="kasen"><label><input type="checkbox" name="chk2" value="chk2" <?php  if($chk2) echo "checked" ;?>>検索に含む</label><span class="zyoubu">商品名</span><input type="text" name="item_name" value="<?php echo $item_name; ?>" size="30"></div>

	<?php
	if ($chk2 && !empty($item_name)) {
		$sql.= " and (" ;
	$search_keywords = preg_split('/[\p{Z}\p{Cc}]++/u', $item_name, -1, PREG_SPLIT_NO_EMPTY);
	  foreach ($search_keywords as $key => $val) {
	  	if($key == 0){
	  		$sql .= "item_name LIKE '%" . $val . "%'";
	  	}else{
	    $sql .= "or item_name LIKE '%" . $val . "%'";
	  	}
	  }
	  $sql.= ")" ;
	}
	?>
		<div class="kasen"><label><input type="checkbox" name="chk3" value="chk3" <?php  if($chk3) echo "checked" ;?>>検索に含む</label><span class="zyoubu">価格</span><input type="number" style="width:80px;" name="A_price" value="<?php echo $A_price; ?>" size="6" min="0">円　～　<input type="number" style="width:80px;" name="B_price" value="<?php echo $B_price; ?>" size="2" min="0">円</div>
		<?php  if($chk3) {
					$sql.= " and price >=". $A_price ." and price <= $B_price ";
				}
				?>
		<div><label><input type="checkbox" name="chk4" value="chk4" <?php  if($chk4) echo "checked" ;?>>検索に含む</label>
		<span class="zyoubu">カテゴリ</span>
			<select name="genre" id="menu1">
				<option value="all" <?php if(isset($_POST["genre"]) && $_POST["genre"] == "all") echo "selected";?>>すべての商品</option>
				<option value="animal" <?php if(isset($_POST["genre"]) && $_POST["genre"] == "animal") echo "selected";?>>動物</option>
				<option value="season" <?php if(isset($_POST["genre"]) && $_POST["genre"] == "season") echo "selected";?>>季節</option>
				<option value="food" <?php if(isset($_POST["genre"]) && $_POST["genre"] == "food") echo "selected";?>>食べ物</option>
				<option value="view" <?php if(isset($_POST["genre"]) && $_POST["genre"] == "view") echo "selected";?>>風景</option>
			</select>
			<select id="menu2" class="select" name="tag">

				<option value="alltag">すべてのタグ</option>
			</select>
		</div>
		<?php  if($chk4 && !empty($genre) && $genre != "all") {
			$sql.= " and item_genre =". '"'.$genre.'"' ;
		}
		?>
		<?php  if($chk4 && $tag && !empty($tag) && $tag != "all" && $tag != "alltag") {
			$sql.= " and tag =". '"'.$tag.'"' ;
		}
		?>
		
			
<!-- チェック欄の検索 -->
		<input class="bigbtn" type="submit" onclick="main.php" value="絞り込む" action="item_list.php" method="post" id="">
	</form>
	<br>
<table>
	<thead>
		<tr><th>画像</th><th>商品ID</th><th class="table_name">商品名</th><th>価格</th><th class="table_genre">カテゴリ</th><th>タグ</th><th>編集</th></tr>
	</thead>
		<tbody>
<?php

foreach ($dbh->query($sql) as $row) {
	
	?><tr><td><img data-action="zoom" src="../images/thumbnails/owners/<?= $row['customer_code']?>/<?= $row['thumbnail']?>" width="70" height="60" alt="picture"></td>
	<td align="center"><?php print($row['item_code']);?></td>
	<td align="center"><?php print($row['item_name']);?></td></span>
	<td align="center"><?php print($row['price']);?></td>
	<td align="center"><?php print($genre_list[$row['item_genre']]);?></td>
	<td align="center"><?php print($tag_list[$row['tag']]);?></td>
		<td align="center"><form method="GET" action="permission.php"><input type="hidden" name="item_code" value="<?php echo $row['item_code']?>">
		<input type="submit" value="編集" ><br></form></td></tr>
<?php
} ?>
	</tbody>
</table>
<?php
$dbh =null;
?>
	<br>
	<button class="backbtn" onclick="location.href='managermenu.php'">管理メニューに戻る</button>
	<footer>
        &copy;Picstock
  </footer>
	</div>
	</main>
	
	<div id="nav"></div>

 </body>
</html>
