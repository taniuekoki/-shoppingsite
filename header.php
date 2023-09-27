<?php
//2023_02_13 headerロゴ挿入　レスポンシブ対応　喜多
//23-1-25 10:06 taniue
//23-01-29 22:00 fujimoto
//23-02-07 16:00 fujimoto カートカウント修正
//2023-02-09 11:00	fujimoto 検索欄修正
require_once './module/UserLogic.php';
$login_check = UserLogic::checkLogin();
$cart_count=0;
if (isset($_SESSION["cart"])) {
	if(count($_SESSION["cart"]) >=10){
		$cart_count = 10;
	}else{
		$cart_count = count($_SESSION["cart"]);
	}
}


/** 検索フォーム処理 **/
$form_keywords = "";
$form_select = "keyword";
if(isset($_GET["keywords"]) && !empty($_GET["keywords"])){
	$form_keywords = $_GET["keywords"];
	$form_select = "keyword";
}

$form_genre = "";
if(isset($_GET["genre"]) && !empty($_GET["genre"])){
	$form_genre = $_GET["genre"];
	$form_select = "genre";
}

$form_tag = "";
if(isset($_GET["tag"]) && !empty($_GET["tag"])){
	$form_tag = $_GET["tag"];
}
/** 検索フォーム処理ここまで **/
?>
<!DOCTYPE html>
<html lang = "ja">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title>header</title>
		<link rel="stylesheet" href="css/header.css">
		<link href="https://fonts.googleapis.com/earlyaccess/nikukyu.css" rel="stylesheet"><!-- webフォント font-family: "Nikukyu"-->
		<script>const form_genre = "<?= $form_genre ?>"</script>
		<script>const form_tag = "<?= $form_tag ?>"</script>
		<script src="js/header.js"></script>
	</head>
	
	<body>
		
		<div id="he">
			
			<a href="./">
				<img id="rogo" src="images/top1.png" alt="rogo">
				<!-- <img src="" alt=""> -->
			</a>
			
			<div id="log">
				<?php 
					if($login_check){
				?>
				<a href="./mypage.php"><?=$_SESSION['login_user']['user_name']?> さん</a>
				<?php 
					}else{
				?>
				<a href="register.php">新規登録</a>
				&emsp;&emsp;<a href="login.php">ログイン</a>
				<?php }?>
			</div>
		</div>
		
		<nav id="nav">
			
			<ul>
				<!-- <div class="jj"> -->
				<li class="hidari">
				
					<div class="text text01">
						<script>const form_select = "<?= $form_select ?>"</script>
						
						<form class="search" action="search.php" method="GET">
							<input type="text" size="50px" placeholder="キーワードで検索 (and または + で絞込検索)" name="keywords" value="<?= $form_keywords?>">
							<button type="submit">検索</button>
						</form>
					</div>
					
					<div class="text text02">
						<div class="sel">
							
							<div class="selectwrapp">
								<select id="menu1" class="select" name="genre" form="tag_seach">
	<!-- 							    <option value="all">選択してください</option> -->
									<option value="all" <?php if($form_genre=="all")echo "selected" ?>>すべての商品</option>
									<option value="animal" <?php if($form_genre=="animal")echo "selected" ?>>動物</option>
									<option value="season" <?php if($form_genre=="season")echo "selected" ?>>季節</option>
									<option value="food" <?php if($form_genre=="food")echo "selected" ?>>食べ物</option>
									<option value="view" <?php if($form_genre=="view")echo "selected" ?>>風景</option>
	<!-- 							    <option value="others">その他</option> -->
								</select>
							</div>
							
							<div class="selectwrapp">
								<select id="menu2" class="select" name="tag" form="tag_seach">
									<option value="alltag">すべてのタグ</option>
								</select>
							</div>
							
							<form class="search"  action="search.php" method="GET" id="tag_seach">
								<button type="submit" id="tag_search_btn" form="tag_seach">検索</button>
							</form>
						</div>
					</div>
					<p class="rb">
						<label><input type="radio" name="kensaku" id="a" value="商品名" checked="checked">商品名検索</label>
						<label><input type="radio" name="kensaku" id="b" value="カテゴリ">カテゴリ検索</label>
					</p>
				</li>
				
				<div class="jj">
					<li class="icon an home"><a href="index.php"><img src="images/home.png" alt="ホーム"><br><span class="me">ホーム</span></a></li>
					<li class="icon an"><a href="favorite.php"><img src="images/hart.png" alt="お気に入り"><br><span class="me">お気に入り</span></a></li>
					<li class="icon an"><a href="cart.php" ><span id="cart"><img  src="images/cart_icon/cart<?=$cart_count?>.png" alt="カート"></span><br><span class="me">カート</span></a></li>
					
					<div class="mm">
						<li class="icon">
							<div id="ii">
								<div class="hamburger-menu">
									<span class="hamburger-menu__line"></span>
								</div><!-- .hamburger-menu -->
								<!-- <a href="#">メニュー</a><span class="me"></span> -->
								<div class="me">メニュー</div>
									<nav class="nav-sp">
										<ul id="uull"><!--ここが出てきます(ドロップダウン)-->
											<li><a href="mypage.php">マイページ</a></li>
											<?php if ($login_check){?>
											<li><a href="logout.php">ログアウト</a></li>
											<?php }?>
											<li><a href="contact.php">問い合わせ</a></li>
										</ul>
									</nav><!-- .nav-sp -->
								</div>
							
						</li>
					</div>
				</div>
			</ul>
		</nav>
	<div id="space"></div>
</body>
  
</html>