<?php

//2023-02-27 fujimoto 修正
//2023-03-07 fujimoto キャンページURL修正
session_start();

//ログインチェック
if(!isset($_SESSION["admin_user"]["user_id"])){
	header("Location:./managerlogin.php");
	exit();
}


?>
<!doctype html>
<head>
    <meta charset="UTF-8">
    <title>Picstock - Management</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/managermenu.css">
    <script src="js/jquery-2.1.4.min.js"></script>
</head>
    <body>
    <main>
    	
    	<div class="innerwrap">
    		<header>
    			<img id="logo" src="../images/top1.png">
    		</header>
    		<h1>- 管理者メニュー -</h1>
    		<ul>
                <li><a href="achievement.php">売上管理</a></li>
                <li><a href="itemlist.php">商品管理</a></li>
                <li><a href="accountlist.php">アカウント管理</a></li>
            	<li><a href="campaign.php">キャンペーン管理</a></li>
            </ul>
            
 	<?php 
 	?>
					<br>
 		      <a href="./managerlogout.php" class="gray">管理者ログアウト</a>
    		</div>
    	 </main>
    	 <footer>
                &copy;Picstock
         </footer>
    </body>
</html>