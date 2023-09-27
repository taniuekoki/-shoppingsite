<?php

//2023-03-08 fujimoto 16:30修正



//不具合?メモ20230308 GET受け取り時タグ選択肢が1つのみ、カテゴリを1度いじると変更可能になる

session_start();
chdir("../"); // カレントディレクトリの変更

require_once './module/connect.php';

// //ログインチェック
// if(!isset($_SESSION["admin_user"]["user_id"])){
// 	header("Location:./managerlogin.php");
// 	exit();
// }

//変数宣言
$current_item_code = "";
$edit_mode = false;

//編集モードか新規モードか判定
if (isset($_GET['item_code'])) {
	$current_item_code = $_GET['item_code'];
	$edit_mode = true;
}

//実行ボタン
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	echo "POSTで来た<br>";
	if(isset($_POST["edit_btn"])){
		echo "編集ボタン";
		exit;
	}elseif(isset($_POST["add_btn"])){
		echo "新規登録ボタン";
		exit;
	}
	if(isset($_POST["delete_btn"])){
		echo "削除ボタン";
		exit;
	}
}

// 	/*** ここからテスト用DB接続***************/
// $dsn = "mysql:host=localhost;dbname=web22k2g2;charset=utf8mb4";
// $user = "root";
// $pass = "";
// $dbh = new PDO ( $dsn, $user, $pass );

// try {
// 	$dbh = new PDO ( $dsn, $user, $pass );

// 	$dbh->query ( 'SET NAMES utf8mb4' );
// } catch ( PDOException $e ) {
// 	print ('Error:' . $e->getMessage ()) ;
// 	die ();
// }
// /****************ここまでDB接続***************/
// $db = $dbh;

//require_once './module/taglist.php';
/************ここからtaglist************/

//分類日本語化リスト

$genre_list = ["all" => "すべての商品", "animal" => "動物", "season" => "季節", "food" => "食べ物", "view" => "風景"];


//タグ日本語化リスト

$tag_list = null;

//動物
$tag_animal = ["dog" => "犬", "cat" => "猫", "others" => "その他"];

//季節
$tag_season = ["spring" => "春", "summer" => "夏", "autumn" => "秋", "winter" => "冬"];

//食べ物
$tag_food = ["cooking" => "料理", "sweets" => "お菓子", "fruits" => "フルーツ", "others" => "その他"];

//風景
$tag_view = ["others" => "その他"];


/** 統合 **/
$tag_list = $tag_animal + $tag_season + $tag_food + $tag_view + array("alltag" => "すべてのタグ", "all" => "すべて");

/***************ここまでタグ名リスト************/

//ログインチェック、テスト環境下では一時無効化中
/*if (!isset($_SESSION["admin_user"]["user_id"])) {
	header("Location:./managerlogin.php");
	exit();
}*/

// GETでitem_code送られたら編集モード、なければ新規登録モード

if ($edit_mode) {
	// 編集モード
	$id = $_GET['item_code'];
	$arr[] = $id;

	$sql = "SELECT * FROM k2g2_item WHERE item_code = ?";
	$result = db_execution($sql, $arr);
	if ($result) {
		$item = $result->fetch();
	}
	// $stmt = $dbh->prepare($sql);
	// $stmt->bindParam(1, $id, PDO::PARAM_INT);
	// $stmt->execute();
	// $row = $stmt->fetch();


} else {
	// 新規登録モード
	// $sql = "INSERT INTO items(name, price, item_genre, tag) VALUES (?, ?, ?, ?, ?)";
	// $stmt = $pdo->prepare($sql);
	// $stmt->execute([$name, $price, $genre, $tag]);
}

//$sql = "select * from k2g2_item where 1=1 and item_code = 10024;";

?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="./css/permission.css">
	<script src="./js/jquery-2.1.4.min.js"></script>
	<script src="./js/permission.js"></script>
	<script src="./js/jquery.uploadThumbs.js"></script>
	<!-- <script src="./js/zoom.js"></script> -->
	<script>let form_genre = "<?= $item['item_genre'] ?>"; </script>
	<script>let form_tag = "<?= $item['tag'] ?>"; </script>

	<title>Picstock - Management</title>
</head>

<body>
	<main>
		<div class="main_wrap">
			<h1 class="center">商品編集画面</h1>
			<p class="center method">
				<?php 
				if (isset($_GET['item_code'])) {
					if ($item['sell_enable'] == 1) {
						// 公開中sell_enable == 1
						// 非公開sell_enable == 0
						echo "【現在：公開中】";
					} else {
						echo "【現在：公開停止中】";
					}
				} else { 
						echo "<span style='color:green'>【新規登録】</span>";
				} ?>

			</p>
			**ふぉーむkokokara
			<form action="<?= $_SERVER["SCRIPT_NAME"] ?>" method="post" enctype="multipart/form-data">
				<div class="main">
					<div class="contentA center">
						<div class="image_box flex">

							<div class="thumbnail_image_box">
								<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
								<label>サムネイル画像</label>
								<input type="checkbox" name="checked" value="1"  checked="checked" id="image_check1"/>
								<small>現在の画像を使用</small><br />
										<?php
										//新規？編集？
										if(isset($_GET['item_code'])){
											$thumbnail_url = "../images/thumbnails/owners/".$item['customer_code']."/".$item['thumbnail'];
										}else{
											$thumbnail_url = "./images/blankimage.png";
										}
										?>
								<img src="<?= $thumbnail_url ?>" class="uploaded thumb thumbnail_image" alt="" /><br>
								<input type="file" name="thumbnail_image"/>
							</div>

							<div class="download_image_box">
								<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
								<label>ダウンロード画像</label>
								<input type="checkbox" name="checked" value="1"  checked="checked" id="image_check2"/>
								<small>現在の画像を使用</small><br />
										<?php
										//新規？編集？
										if(isset($_GET['item_code'])){
											$download_url = "../../download_images/owners/".$item['customer_code']."/".$item['image'];
										}else{
											$download_url = "./images/blankimage.png";
										}
										?>
								<img src="<?= $download_url ?>" class="uploaded thumb thumbnail_image" alt="" /><br>
								<input type="file" name="download_image" />
							</div>
						</div>
					</div>


					<div class="contentB">

						<p>
							商品名　
							<?php if (isset($item['item_name'])) { ?>
								<input type="text" name="item_name" value="<?php echo $item['item_name']; ?>"
									style="height: 2em; width: 300px; border-radius: 8px; border: 1px solid black">
							<?php } else { ?>
								<input type="text" name="item_name"
									style="height: 2em; width: 300px; border-radius: 8px; border: 1px solid black">
							<?php } ?>
						</p>
						<p>
							価格　　
							<?php if (isset($item['price'])) { ?>
								<input type="number" name="price" value="<?php echo $item['price']; ?>" min="0" step="10">
							<?php } else { ?>
								<input type="number" name="price" value="0" min="0" step="10">
							<?php } ?>
						</p>
						<p>カテゴリ
							<select name="genre" id="menu1" style="height: 2em;">
								<option value="all" <?php if (!isset($item['item_genre']))
									echo " selected"; ?>>カテゴリを選んでください</option>
								<option value="animal" <?php if (isset($item['item_genre']) && $item['item_genre'] == "animal")
									echo " selected"; ?>>動物</option>
								<option value="season" <?php if (isset($item['item_genre']) && $item['item_genre'] == "season")
									echo " selected"; ?>>季節</option>
								<option value="food" <?php if (isset($item['item_genre']) && $item['item_genre'] == "food")
									echo " selected"; ?>>食べ物</option>
								<option value="view" <?php if (isset($item['item_genre']) && $item['item_genre'] == "view")
									echo " selected"; ?>>風景</option>
							</select>
						</p>
						<p>タグ　　 <select id="menu2" class="select" name="tag" style="height: 2em;">
								<?php if (isset($tag_list[$item['tag']])) { ?>
									<option value="<?php $tag_list[$item['tag']] ?>"><?php echo $tag_list[$item['tag']] ?></option>
								<?php } else { ?>
									<option value="alltag">タグを選んでください</option>
								<?php } ?>
							</select>
					</div>
				</div>

				<p class="center">
					<select name="" style="height: 3em; width: 150px;">
						<option>公開する</option> <!-- $sql.=でupdate k2g2_item set sell_enable = 1にする -->
						<option>公開停止</option> <!-- $sql.=でupdate k2g2_item set sell_enable = 0にする -->
					</select>
				</p>
				<br>
				<p class="center"><button type="submit" name="delete_btn">商品削除</button>
					
						<?php if ($edit_mode) { ?>
							<button type="submit" name="edit_btn">編集完了</button>
						<?php } else { ?>
							<button type="submit" name="add_btn">新規登録</button>
						<?php } ?>
					</button>
				</p>
				<br>
			</form>
			<div class="center">
				<form action="./itemlist.php" method="POST">
					<button class="backbtn" type="submit" name="resume">中止して商品管理リストに戻る</button>
				</form>
			</div>
			<div class="right">
				<button class="backbtn" onclick="location.href='./managermenu.php'">管理メニューに戻る</button>
			</div>
			<footer>
				&copy;Picstock
			</footer>
		</div>
	</main>







</body>

</html>