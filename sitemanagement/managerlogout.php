<?php
session_start();
    $_SESSION = NULL;
    
    if (isset($_COOKIE["PHPSESSID"])) {
        setcookie("PHPSESSID", '', time() - 1800, '/');
    }
    
    session_destroy();
    
//     header("Location:./managerlogin.php");
    
    
 ?>
 
<!doctype html>
<head>
    <meta charset="UTF-8">
    <title>Picstock - Management</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/managermenu.css">
</head>
    <body>
    <div class="innerwrap">
    
    	<main>
        <header>
    	<img id="logo" src="../images/top1.png">
        </header>
			<p class="margin">管理ページからのログアウトが完了しました。<br><br>
    		
    		<p><a href="../" >Pocstockポータルへ</a></p><br>
    		<p><a href="./managerlogin.php" >管理者ログイン画面へ</a></p>
    	</main>
    </div>
    <footer>
        &copy;Picstock
    </footer>
    </body>
</html>