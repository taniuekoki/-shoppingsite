<?php
//2023-03-01 16:00 taniue 初期制作
//2023-03-03 11:26 fujimoto ファイル関連修正後
session_start();

$result_msg = array();//リザルトメッセージの初期化
$file_uploaded = false;//ファイルがアップロードされているか


//ログインチェック
if (!isset($_SESSION["admin_user"]["user_id"])) {
	header("Location:./managerlogin.php");
	exit();
}
chdir("../"); //カレントディレクトリの変更
include './module/connect.php';
include './module/taglist.php';
$err_flg = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$db->beginTransaction();	//トランザクション開始
	while(true){
		if(isset($_POST["hold_btn"])){
			//キャンペーン開催（更新）ボタン処理
			$arr = array();
			$arr[] = $_POST["cam_name"];
			$arr[] = $_POST["cam_detail"];
			$arr[] = $_POST["cam_ratio"];
			$arr[] = $_POST["cam_left_count"];
			
			/*ファイル処理↓*/
			//ファイルサイズチェック
			if($_FILES['images']["error"] == 2){
				$err_flg = 1;
				echo "ファイルサイズオーバー";
				$result_msg[] = "<p style='color:red' class='center msg'>画像のファイルサイズが3MBを超えています。</p>";
				break;//rollbackへ向かう
			}

			if(is_uploaded_file($_FILES["images"]["tmp_name"])){
				//アップロードされているとき
				$file_uploaded = true;
				if($_FILES['images']["error"] != 0){
					$err_flg = 1;
					echo "アップロード失敗";
					break;//rollbackへ向かう
				}else{
					//アップロード成功した時
					echo "アップロード成功";
					//POSTされたデータ取得
					$file_name = $_FILES["images"]["name"];
					$file_info = pathinfo($file_name);
					$file_tmp_path = $_FILES["images"]["tmp_name"];	//一時保存のパスを代入
					//拡張子のチェック
					$file_extension = $file_info["extension"];
					$file_ext_check = strtolower($file_extension);
					if($file_ext_check != "jpg" && $file_ext_check != "jpeg" && $file_ext_check != "png" && $file_ext_check != "gif" && $file_ext_check != "webp"){
						$result_msg[] = "<p style='color:red' class='center msg'>アップロードできる画像形式ではありません。</p>";
						$err_flg = 1;
					}elseif($_FILES["images"]["size"] == 0){
						$result_msg[] = "<p style='color:red' class='center msg'>ファイルが存在しないか無効なファイルです。</p>";
						$err_flg = 1;
					}

					//バナー画像用SQL(名前はそのまま。拡張子のみ変更)
					$file_sql = ",sale_banner = 'campaign_bunner.".$file_extension."'";
				}
			}else{
				//変更なければ画像部分UPDATEしない
				$file_sql = "";
			}

			$post_data = array();
			// $_POSTされた内容を key を変数名としながら一括代入
			foreach ($_POST as $key => $value) {
				$post_data[$key] = $value;
			}
			// foreach($post_data as $key => $val){
			// 	echo "key:".$key." value:".$_POST[$key]."<br>";
			// }

			$item_sql = cam_sql($post_data);

				/*ファイル処理↑*/

			if($_POST["hold_btn"] != "キャンペーン開催"){
				//開催時

			}else{
				//更新時
				//discountテーブルの処理
				$sql = "update k2g2_discount SET campaign_name = ?,sale_detail = ?,sale_ratio= ?,sale_limit= ? ".$file_sql;
				echo "セールSQL:".$sql."<br>";
				$result = db_execution($sql,$arr);
				echo "<br>discountアップデート実行しました<br>";
				if(!$result){
					echo "<br>discountアップデート失敗<br>";
					$err_flg = 1;
					break;//rollbackに向かう
				}

				//itemテーブルの処理
				$arr2=array();
				$arr2[] = $_POST["cam_left_count"];
				foreach($item_sql[1] as $val){
					$arr2[] = $val;
				}
				foreach($arr2 as $val){
					echo $val."<br>";
				}
				$sql2 = "update k2g2_item set sale_flag = 1 ,sale_limit = ? where item_code IN (".$item_sql[0].")";
				echo $sql2;
				$result = db_execution($sql2,$arr2);
				if(!$result){
					echo "<br>itemアップデート失敗<br>";
					$err_flg = 1;
					break;//rollbackに向かう
				}
				//商品マスタのアップデート処理
				//成功したら・・・↓、失敗したらエラーフラグたててbreak
				if($file_uploaded){
					//move_uploded_fileでファイル上書き処理
					$move_path = "./images/campaigns/campaign_bunner.".$file_extension;
					$move_flg = move_uploaded_file($file_tmp_path,$move_path);
					if(!$move_flg){
						//ファイルの上書きに失敗
						$err_flg = 1;
						break;//rollbackに向かう
					}
				}

			}
		}else{
			//キャンペーン削除ボタン処理

		}
		break;	//while終了
	}//while end

	if($err_flg){
		echo "ロールバック";
		$db->rollBack();
		$result_msg[] = "<p style='color:red' class='center msg'>エラーが発生しました。情報は更新されていません。</p>"; 
	}else{
		echo "コミット";
		$db->commit();	//コミット
		$result_msg[] = "<p style='color:blue' class='center msg'>正常に処理が完了しました。</p>";
	}

}
/*セールしている商品があるか判定*/
$arr=array();
$sql="select sale_flag from k2g2_item where sale_flag = 1";
$result = db_execution($sql,$arr);
$sale_flg = $result->fetch();

if($sale_flg[0]){
  //セールしていれば
  $arr=array();
  $sql="select * from k2g2_discount";
  $result = db_execution($sql,$arr);
  $sale_info = $result->fetch();

  $campaing_name = $sale_info["campaign_name"];
  $sale_detail = $sale_info["sale_detail"];
  $sale_ratio = $sale_info["sale_ratio"];
  $sale_limit = $sale_info["sale_limit"];
	$sale_bunner = $sale_info["sale_banner"];
	$sale_display = "キャンペーン開催";
}else{
  //セールしていなければあんでふぁいんど対策でnull
  $campaing_name=$sale_detail=$sale_ratio=$sale_limit="";
	$sale_display = "キャンペーン更新";
}

function  cam_sql ($post_data){
	$sql = "select item_code from k2g2_item where deleted_item = 0 and sell_enable = 1 ";
	$arr = array();
	//全品取得の場合は以下のifは通らない。
	
	//現在のセール対象商品取得
	if(isset($post_data["current_sale"])){
		$sql .= "and sale_flag = 1";
	}
	
	//値段下限と上限 絞り込みONの場合
	if(isset($post_data["item_price_low"]) && isset($post_data["item_price_high"])){
		$sql .= "and price between ? and ? ";
		$arr[] = $post_data["item_price_low"];
		$arr[] = $post_data["item_price_high"];
	}
	
	//ジャンル 絞り込みONの場合
	if(isset($post_data["genre"]) && isset($post_data["tag"])){
		if($post_data["genre"] != "all"){
			$sql .= "and item_genre = ? ";
			$arr[] = $post_data["genre"];
		}
		if($post_data["tag"] != "all"){
			$sql .= "and tag = ? ";
			$arr[] = $post_data["tag"];
		}
	}
	$sql_arr[0] = $sql;
	$sql_arr[1] = $arr;
	return $sql_arr;
}

?>
<!DOCTYPE html>
<html lang="ja">
	<head>
	<meta charset="UTF-8">
	<title>Picstock - Management</title>
	<!-- <link rel="stylesheet" href="css/reset.css"> -->
	<!--     <link rel="stylesheet" href="css/zoom.css"> -->
	<link rel="stylesheet" href="css/campaign.css">
	<script src="./js/jquery-2.1.4.min.js"></script>
	<script src="./js/zoom.js" type="text/javascript"></script>
  <script src="./js/campaign.js"></script>
  <script src="./js/uploadThumbs.js"></script>
	<script src="../module/js/taglist.js"></script>
	</head>
	<body>
		<main>
			<a href="./managermenu.php"  id="logo"><img  src="../images/top1.png"></a>
			<h1>キャンペーン管理</h1>
<?php 
//メッセージ表示
if(count($result_msg) > 0){
	foreach($result_msg as $msg){
		echo $msg;
	}
}

?>
			<div class="cam_editer">
				<form name="k2" action='<?= $_SERVER["SCRIPT_NAME"]?>' method="post" enctype="multipart/form-data">
					<label>キャンペーン名</label>
					<input type="text" name="cam_name" id="cam_name" value="<?=$campaing_name?>">
					<div class="line"></div>
					<label>内容</label>
					<input type="text" name="cam_detail" id="cam_detail" value="<?= $sale_detail?>">
					<div class="line"></div>
					<div class="center">
						<label>バナー</label>
            <input type="checkbox" name="checked" value="1" checked="checked" />
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728"
						<small>現在の画像を使用</small><br />
            <img src="../images/campaigns/<?=$sale_bunner?>" class="uploaded thumb" alt="" /><br>
            <input type="file" name="images" />
					</div>
					<div class="line"></div>
					<label>割引率</label>
					<input type="text" name="cam_ratio" id="cam_ratio" value="<?= $sale_ratio?>"><span id="parsent">&#37;</span>
					<div class="line"></div>
					<label>割引限定数残り</label>
					<input type="text" name="cam_left_count" id="cam_left_count" value="<?= $sale_limit?>">
					<div class="line"></div>

					<div class="flex item_chk_wrap center">
						<label class="chklabel" for="sel_now">現在の対象商品</label><input type="radio" id="sel_now" name="item_select" value="current_sale">
						<label class="chklabel" for="sel_all">全品</label><input type="radio" id="sel_all" name="item_select" value="all_item" checked>
						<label class="chklabel" for="sel_choice">絞り込み</label><input type="radio" id="sel_choice" name="item_select" value="choice">
					</div>

					<div class="item_select_wrap">
						<div class="flex center">
							<input type="checkbox" name="item_sel_price" id="item_sel_price"><label for="item_sel_price">価格</label>
							<input type="text" class="input_price" name="item_price_low" size="10" value="0">円～
							<input type="text" class="input_price" name="item_price_high" size="10" value="999999">円
						</div>
						<div class="flex center">
							<input type="checkbox" name="item_sel_category" id="item_sel_category"><label for="item_sel_category">カテゴリ</label>
							<select id="menu1" class="select" name="genre">
								<option value="all">すべての商品</option>
								<option value="animal">動物</option>
								<option value="season">季節</option>
								<option value="food">食べ物</option>
								<option value="view">風景</option>
							</select>
							<select id="menu2" class="select" name="tag">
								<option value="all">すべてのタグ</option>
							</select>
						</div>
					</div>
					<div class="campaign_target_product_wrap"><br>
						<button name="campaign_list_btn" id="item_check_btn">キャンペーン対象商品確認</button><br>
						<div class="line"></div>
						<input type="submit" name="delete_btn" value="キャンペーン削除">
						<input type="submit" name="hold_btn" value="キャンペーン開催">
						<div class="line"></div>
						<h2>キャンペーン対象商品一覧</h2><p class="result_count"></p>
						<table class="item_list_table">
							<!-- ajaxでappendする -->
						</table>
						<div class="line"></div>
						<a href="./managermenu.php"><button name="menu_btn">管理メニューへ戻る</button></a>
					</div>
				</form>
			</div>
		</main>
	</body>
</html>