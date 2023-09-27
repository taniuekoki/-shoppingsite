<?php 
//2023-02-27 たにうえ

chdir("../"); //カレントディレクトリの変更

session_start();
//ログインチェック
if (!isset($_SESSION["admin_user"]["user_id"])) {
	header("Location:./managerlogin.php");
	exit();
}
include './module/connect.php';
include './module/taglist.php';
$table_flg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//総売り上げｃｓｖボタンクリック時
	if(isset($_POST["totalproceeds_csv"])){
		if (isset($_SESSION["total_proceeds_arr"])) {
			$arr = $_SESSION["total_proceeds_arr"];
		}
		$f_name = "total_proceeds";		//ファイルパス用
		$table = "totalproceeds";		//ダウンロード処理判定用
		//csvダウンロード関数
		csv_download($f_name,$arr,$table);
	}
	
	//カテゴリｃｓｖボタンクリック時
	if(isset($_POST["category_csv"])){
		$f_name = "category";		//ファイルパス用
		$table = "category";		//ダウンロード処理判定用
		if (isset($_SESSION["category_arr"])) {
			$arr = $_SESSION["category_arr"];
		}
		//csvダウンロード関数
		csv_download($f_name,$arr,$table);
	}
	if (isset($_POST["flg"])) {
		$table_flg = $_POST["flg"];
	}
}

//csvダウンロード関数
function csv_download($f_name,$arr,$table) {
	
	//ファイル名指定
	$time = time();
	$filename = './sitemanagement/csv/'.$f_name . $time . '.csv';
	
	//ファイルの存在チェック
	if(!touch($filename)) {
		echo 'すでにファイルが存在します';
		exit;
	}else{
		$outputs = '';
		switch ($table) {
			case "totalproceeds":
				//総売り上げ表のｃｓｖダウンロード処理
				foreach ($arr as $arrRow) {
					foreach ($arrRow as $arrValue) {
						$arrValue = mb_convert_encoding($arrValue, "SJIS", "UTF-8");
						$arrValue = "=\"$arrValue\"";
						$outputs .= $arrValue . ',';
					}
					$outputs = rtrim($outputs, ',') . "\n";
				}
				break;
			case "category":
				//カテゴリ別売り上げ表のｃｓｖダウンロード処理
				foreach ($arr as $arrRow) {
					foreach ($arrRow as $arrValue) {
						$arrValue = mb_convert_encoding($arrValue, "SJIS", "UTF-8");
						$arrValue = "=\"$arrValue\"";
						$outputs .= $arrValue . ',';
					}
					$outputs = rtrim($outputs, ',') . "\n";
				}
				break;
		}
		file_put_contents($filename, $outputs);
	}
	// ファイルのコンテンツタイプを指定
	header('Content-Type: application/octet-stream ');
	// ファイルのダウンロードバーを表示; ダウンロード後のファイル名を設定
	header('Content-Disposition:attachment;filename = '.$filename);
	// ファイルの大きさを明示
	header('Content-Length: '.filesize($filename));
	// ファイルを出力
	echo file_get_contents($filename);
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/charts.css/dist/charts.min.css">
<link rel="stylesheet" href="css/achivment_style.css">
<script src="js/jquery-2.1.4.min.js"></script>
<script src="js/achivment_script.js"></script>
<input type="hidden" name="table_flg" value="<?=$table_flg?>">
</head>
<body>
<main>
<header>
 <a href="./managermenu.php" id="logo" ><img src="../images/top1.png"></a>
 <h1>売上管理画面</h1>
</header>
  <ul>
    <li><a class="a" href="#tab1" class="current">総売り上げ表</a></li>
    <li id="boder"><a class="a" href="#tab2">カテゴリ別売り上げ表</a></li>
  </ul>
  
  <div id="contents">
  <!--------------------------------総売り上げ表↓--------------------------------------->
    <div id="tab1" class="tab">
	    <form action="<?= $_SERVER["SCRIPT_NAME"] ?>" method="post">
				
		    	<button name="totalproceeds_csv" class="btn csv">CSV出力</button>

    	<?php include './sitemanagement/totalproceeds.php';?>
    	<input type="hidden" name="flg" value="0">
    	</form>
    	
    </div>
    <!--------------------------------総売り上げ表↑--------------------------------------->
    
    <!--------------------------------カテゴリ別売り上げ表↓--------------------------------------->
    <div id="tab2" class="tab">
      <form action=" <?= $_SERVER["SCRIPT_NAME"] ?>" method="post">
		  <button name="category_csv" value="" class="btn csv">CSV出力</button>
     <?php include './sitemanagement/category.php';?>
       <input type="hidden" name="flg" value="1">
      </form>
    </div>
    <!--------------------------------カテゴリ別売り上げ表↑--------------------------------------->
  <a href="./managermenu.php" ><button class="btn menubackbtn">管理者メニューに戻る</button></a>
  </div>
</main>
<footer>
   &copy;Picstock
</footer>
</body>
</html>