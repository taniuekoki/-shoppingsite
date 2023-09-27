<?php
/**
* 検索画面ロジック
* 2023-01-29		fujimoto
* 2023-02-01 		fujimoto セール検索機能追加
* 2023-02-09 11:00	fujimoto 無入力検索時の処理追加
**/

//タグリスト読み込み
require_once './module/taglist.php';
//$genre_list["ジャンル名"]で日本語が返ります
//$tag_list["タグ名"]で日本語が返ります

require_once './module/UserLogic.php';
require_once './module/connect.php';
session_start();

//初期化
$search_keywords_get = "";
$search_keywords = array();
$search_genre = "";
$search_tag = "";
$search_select = "キーワード:";
$result = null;	//DB出力
$row_item_count = 0; //検索結果数
$page = 1;		//ページカウント
$page_end = 1;	//最終ページ(rowCount%10)
$form_sort = "新着順";


//キーワード空欄の時は全商品検索
if(empty($_GET) || empty($_GET["keywords"]) && !(isset($_GET["genre"])||isset($_GET["tag"])||isset($_GET["sale"]))){
	$search_select= "サイト内の";
	
	$sql = "select * from k2g2_item ";
	$arr = array();
	
	//ソート切り替え
	if(isset($_GET["sort"]) && !empty($_GET["sort"])){
		$form_sort = $_GET["sort"];
		$sql .= sort_switch($_GET["sort"]);
	}else{
		$sql .= "order by seller_date desc";
	}
	
	//DB実行
	$result = db_execution($sql,$arr);
	
	//ページ数カウント
	if(isset($result)){
		$row_item_count = $result->rowCount();
		if($row_item_count != 0){
			$page_end = ceil($row_item_count / 15);
		}
	}
	
	
}
//**************キーワード検索****************//
if(isset($_GET["keywords"]) && !empty($_GET["keywords"])){
	$search_keywords_get = $_GET["keywords"];
	$search_select = "キーワード:";
	
	
	
	
	
	//空白文字で区切ってキーワードの配列化
	$search_keywords = preg_split('/[\p{Z}\p{Cc}]++/u', $_GET["keywords"], -1, PREG_SPLIT_NO_EMPTY);
	
// 	echo "＜キーワード検索＞<br>";
	$arr = array();
	$sql = "select * from k2g2_item where ";
	$pre_and = false;	//前の単語がAND構文だった？
	
	foreach($search_keywords as $key => $val){
		global $sql;
		
		if($key !== 0 && ( $val == "and" || $val == "+" || $val == "AND") ){
			//前の項と後の項のAND検索にする
			$sql .= "AND ";
			$pre_and = true;
			
		}elseif($key !== 0 && $key !== count($search_keywords)-1 && $pre_and != true ){
			//前の項と後の項のOR検索にする
			$sql .= "OR ";
			
			$sql .= "item_name LIKE ? ";
			$keyword = '%'.$val.'%';
			$arr[] = $keyword;	//プレースホルダー配列へ入れる
			$pre_and = false;
			
		}else{
			//最初の単語か最後の単語
			
			//2単語以上、かつ最後の単語で、かつ前がANDじゃなければ
			if(count($search_keywords)>1 && $key == count($search_keywords)-1 && $pre_and != true){
				$sql .= "OR ";
			}
			
			$sql .= "item_name LIKE ? ";
			$keyword = '%'.$val.'%';
			$arr[] = $keyword;	//プレースホルダー配列へ入れる
			$pre_and = false;
		}
	}
	
	//ソート切り替え
	if(isset($_GET["sort"]) && !empty($_GET["sort"])){
		$form_sort = $_GET["sort"];
		$sql .= sort_switch($_GET["sort"]);
	}else{
		$sql .= "order by seller_date desc";
	}	
	
	//データベース検索開始
	$result = db_execution($sql,$arr);
	
	//ページ数カウント
	if(isset($result)){
		$row_item_count = $result->rowCount();
		if($row_item_count != 0){
			$page_end = ceil($row_item_count / 15);
		}
	}
	
}
//**************キーワード検索終****************//

//**************タグ検索****************//
if(isset($_GET["genre"])||isset($_GET["tag"])){
	$search_select = "カテゴリ:";

	if(isset($_GET["genre"])){
		$search_genre = $_GET["genre"];
	}
	if(isset($_GET["tag"])){
		$search_tag = $_GET["tag"];
	}
	
	$sql = "select * from k2g2_item where 1=1 ";
	$arr = array();
	
	if($search_genre != "all"){
		$sql .= "AND item_genre = ? ";
		$arr[] = $search_genre;
	}
	
	if($search_tag  != "alltag" && $search_tag  != "all"){
		$sql .= "AND tag = ?";
		$arr[] = $search_tag;
	}
	
	//ソート切り替え
	if(isset($_GET["sort"]) && !empty($_GET["sort"])){
		$form_sort = $_GET["sort"];
		$sql .= sort_switch($_GET["sort"]);
	}else{
		$sql .= "order by seller_date desc";
	}	
	
	//DB実行
	$result = db_execution($sql,$arr);
	
	//ページ数カウント
	if(isset($result)){
		$row_item_count = $result->rowCount();
		if($row_item_count != 0){
			$page_end = ceil($row_item_count / 15);
		}
	}
}
//**************タグ検索終****************//

//**************セール検索******************//

if(isset($_GET["sale"])){
	$search_select = "セール:";
	$sale_arr = array();
	$sale_sql = "select * from k2g2_item where sale_flag = true and sell_enable = true and deleted_item = false ";
	
	//ソート切り替え
	if(isset($_GET["sort"]) && !empty($_GET["sort"])){
		$form_sort = $_GET["sort"];
		$sale_sql .= sort_switch($_GET["sort"]);
	}else{
		$sale_sql .= "order by seller_date desc";
	}	

	//DB実行
	$result = db_execution($sale_sql,$sale_arr);
	
	if($result){
		//ページ数カウント
		if(isset($result)){
			$row_item_count = $result->rowCount();
			if($row_item_count != 0){
				$page_end = ceil($row_item_count / 15);
			}
			
		}
	}

	
}
//**********セール検索ここまで**************//



//ページ数GET検知
if(isset($_GET["page"])||!empty($_GET["page"])){
	if($_GET["page"] >= 1){
		$page = $_GET["page"];
	}
}


//ソートファンクション
function sort_switch($get_sort){
	$sql = "";
	//ソート
	switch ($get_sort){
		case "新着順":
			$sql .= "order by seller_date desc";
			break;
		case "古い順":
			$sql .= "order by seller_date asc";
			break;
		case "人気順":
			$sql .= "order by favorite desc";
			break;
		case "価格の安い順":
			$sql .= "order by price asc";
			break;
		case "価格の高い順":
			$sql .= "order by price desc";
			break;
	}
	return $sql;
}

?>


<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>検索</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/search.css">
  <script src="js/jquery-2.1.4.min.js"></script>
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
    		<div class="head">
				<p class="headp">
					<?php 	echo $search_select;
						if($search_select == "キーワード:"){
							foreach($search_keywords as $val)echo "<b>".$val." </b>";
						}elseif($search_select == "カテゴリ:"){
							?><?="<b>".$genre_list[$search_genre]."</b>"?> ＞<?="<b>".$tag_list[$search_tag]."</b>"?><?php
						}elseif($search_select == "セール:"){
							echo "<b>セール開催中の商品</b>";
						}else{
							echo "<b>すべての商品</b>";
						}
						?>の検索結果<span>
		 				<?= (($row_item_count) != 0)?($page * 15 - 14): "0" ?>～<?= ($page * 15 >= $row_item_count)? $row_item_count : $page * 15;?>件目表示 <b>(全<?= (!empty($result))? $row_item_count : "0"?>件)</b> </span>
				</p>
				
				
				<div class="selectwrap">
				<form action="search.php" method="GET" id="sortform">
					<?php if($search_select == "キーワード:"){?>
					<input type="hidden" name="keywords" value="<?= $search_keywords_get ?>"><?php
						}elseif($search_select == "カテゴリ:"){?>
					<input type="hidden" name="genre" value="<?= $search_genre ?>">
					<input type="hidden" name="tag" value="<?= $search_tag ?>">
				<?php }elseif($search_select == "セール:"){ ?>
					<input type="hidden" name="sale" value="1">
				<?php } ?>
					<select class="sort" name="sort">
						<option value="新着順" <?php if($form_sort=="新着順")echo "selected" ?>>新着順</option>
						<option value="古い順" <?php if($form_sort=="古い順")echo "selected" ?>>古い順</option>
						<option value="人気順" <?php if($form_sort=="人気順")echo "selected" ?>>人気順</option>
						<option value="価格の安い順" <?php if($form_sort=="価格の安い順")echo "selected" ?>>価格の安い順</option>
						<option value="価格の高い順" <?php if($form_sort=="価格の高い順")echo "selected" ?>>価格の高い順</option>
					</select>
					</form>
				</div>
			</div>
			
        	<div id="slide">
              
              <div class="topPhoto"></div>
                <section class="subPage">
                  <div class="photoGarelly">
                  		
<?php /**************************商品ループ**************************/

if(isset($result)){
//検索結果あり
	
	$i = ($page * 15 - 15);
	$end = ($page * 15 >= $row_item_count)? $row_item_count-1 : $page * 15-1;
	
	$result_items = $result->fetchAll();
	for($i;$i<= $end;$i++){
		$result_item = $result_items[$i];
?>
                  	<div>
                  		<div class="imgdiv"><a href="./item.php?item_code=<?= $result_item["item_code"] ?>"><img src="./images/thumbnails/owners/<?= $result_item["customer_code"] ?>/<?= $result_item["thumbnail"]?>" alt="<?= $result_item["item_name"] ?>"></a></div>
                  		<div class="price"> 
                  		  	<div class="display_haba"><?= $result_item["item_name"] ?></div>
                  		  	<?php 
                  		  	
                  		  	if($result_item["sale_flag"]){
                  		  		echo "<span class='del'>\\".$result_item["price"]."</span>";
                  		  		$arr = array();
                  		  		$sql = "select * from k2g2_discount";
                  		  		$result_discount = db_execution($sql, $arr);
                  		  		$discount_row = $result_discount->fetch();
                  		  		$discount_price = floor($result_item["price"] * ($discount_row["sale_ratio"]/100) );	//値引額(一応)
                  		  		echo '<span class="nebiki">セール価格 \\'.($result_item["price"] - $discount_price).'</span>';	//値引き後価格
                  		  	}else{
                  		  		echo "<span>\\".$result_item["price"]."</span>";
                  		  	}
                  		  	?>
                  		  	
                    	</div>
                    </div>	
<?php	
	
	}
}else{
	//検索0件・検索不能
	echo "検索結果がありません";
}


?>

<?php /**************************商品ループ終**************************/ ?>                 	
                    	
                    	
                    
                  </div>
                </section>
              </div>
              
			<div class="bb">
			<?php if($page != 1){?>
				<form name="btn" action="search.php" method="get" id="prev_page_form">
				<?php if($search_select == "キーワード:"){
					?><input type="hidden" name="keywords" value="<?= $search_keywords_get ?>"><?php
					  }elseif($search_select == "カテゴリ:"){?>
					<input type="hidden" name="genre" value="<?= $search_genre ?>">
					<input type="hidden" name="tag" value="<?= $search_tag ?>">
				<?php }elseif($search_select == "セール:"){ ?>
					<input type="hidden" name="sale" value="1">
				<?php }?>
				<?php if(isset($form_sort))echo '<input type="hidden" name="sort" value="'.$form_sort .'">';?>
					<input type="hidden" name="page" value="<?= ($page > 1)? ($page -1): "1" ?>">
					<input type="submit"  value="＜＜&nbsp;前へ">
				</form>
			<?php }?>
				
				<p class="page_tag"><?= $page ?>&nbsp;/&nbsp;<?= $page_end?>ページ</p>
				
			<?php if($page_end != $page){?>	
				<form name="btn" action="" method="get" id="next_page_form">
				<?php if($search_select == "キーワード:"){?>
					<input type="hidden" name="keywords" value="<?= $search_keywords_get ?>"><?php
					  }elseif($search_select == "カテゴリ:"){?>
					<input type="hidden" name="genre" value="<?= $search_genre ?>">
					<input type="hidden" name="tag" value="<?= $search_tag ?>">
				<?php }elseif($search_select == "セール:"){ ?>
					<input type="hidden" name="sale" value="1">
				<?php }?>
				<?php if(isset($form_sort))echo '<input type="hidden" name="sort" value="'.$form_sort .'">';?>
					<input type="hidden" name="page" value="<?= ($page +1 <= $page_end)? ($page +1): $page ?>">
					<input type="submit"  value="次へ&nbsp;＞＞">
				</form>
			<?php }?>
			
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