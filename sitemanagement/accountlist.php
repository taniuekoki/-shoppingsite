<?php
//2023_02_22 小林

session_start();
chdir("../"); //カレントディレクトリの変更
include("./module/connect.php");
include("./module/taglist.php");

$t = "";
$m = "";

// echo "<br>". $chkno. "<br>";
// echo "<br>". $_SESSION["chkno"]. "<br>";


set_error_handler (function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});


//ログインチェック
    
if (!isset($_SESSION["admin_user"]["user_id"])) {
    //ログインしてなかった場合、管理者ログイン画面に移行する
    header("Location:./managerlogin.php");
    exit();    
}
?>

<!doctype html>
<head>
    <meta charset="UTF-8">
    <title>Picstock - Customer Account Management</title>
    <link rel="stylesheet" href="css/reset.css">
<!--     <link rel="stylesheet" href="css/zoom.css"> -->
    <link rel="stylesheet" href="css/accountlist.css">
    <script src="js/jquery-2.1.4.min.js"></script>
</head>
<body>

	<div class="innerwrap">
		<header>
		</header>
		<main>
			<a href="./managermenu.php" id="logo" ><img src="../images/top1.png"></a>
			<h1>アカウント管理</h1>
				
            <form action="<?= $_SERVER["SCRIPT_NAME"] ?>" method="POST">
                カスタマーコード：
                <input class="text" type="text" name="saku" value="" size="10px" >
                <input class="btn2" type="submit" name="btn" value="検索">
            </form>           
   			<div class="line"></div>   
<?php  
   
   //削除ボタンが押されたら、送信されたカスタマ連番を受け取って削除する。
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["saku"])) {
                if(strlen($_POST["saku"])) {    
                $saku = $_POST["saku"];
                //
                //データベースの読み込み
                $arr = array();
                $arr[] = $saku;               
                $sql = "select customer_code, user_id, nickname, deleted_account from k2g2_customer where customer_code = ? " ;

//                 $sth = $pdo -> query($sql);
//                 $count = $sth -> rowCount();
//                 if ($count == false) {
//                      echo "このユーザーは存在しません";
//                 } else {                     
                    $reslut = db_execution($sql, $arr);
                    $user = $reslut->fetch();
                    if ($user == false) {
                        echo "<br>このユーザーは存在しません";
      ?>
                        <br><br><br>
                        <a id="ikou" href="managermenu.php">管理メニューに戻る</a>
                        <br>
      <?php	
                        exit();
                    } else {
                        $_SESSION["cust"] = $cust = array_reverse($user);                  
                    }
                
                                    
                
//                    $row = mysql_num_rows($reslut);
//                     if ($user == false) { 
//                 try {  
//                 } catch (Exception $e) {
//                     echo "このユーザーは存在しません。". $e->getMessage();     
//                 }
      
        
                    if ($cust["deleted_account"] == false) {
                        $t = "利用中";
                        $m = "アカウント停止";                        
                    } else {
                        $t = "停止中";
                        $m = "アカウント復旧";
                    }
                }
            }           
            
                
            //二重送信防止
        if (isset($_REQUEST["delete"]) == true) {
//             echo "ボタンクリック";
//             echo "<br>". $_REQUEST["chkno"]. "<br>";
//             echo "<br>". $_SESSION["chkno"]. "<br>";
            
            if ((isset($_REQUEST["chkno"]) == TRUE) && (isset($_SESSION["chkno"]) == true)
                && ($_REQUEST["chkno"] == $_SESSION["chkno"]))	
            {                              
                if (isset($_POST["delete"])){
                    if(strlen($_POST["delete"])) {
                        $delete = $_POST["delete"];
                                           
                        $arr = array();
                        $arr[] = $delete;
        //             deleted_accountの値がtrueだったらfalseに、falseだったらtrueに反転させる。
                        $sql2 = "update k2g2_customer set deleted_account = if(deleted_account = 1,0,1) where customer_code = ? " ;                
                        $reslut = db_execution($sql2, $arr);
                        
                        $sql = "select customer_code, user_id, nickname, deleted_account from k2g2_customer where customer_code = ? " ;
                        $reslut = db_execution($sql, $arr);
                        $user = $reslut->fetch();
                        $_SESSION["cust"] = $cust = array_reverse($user);
                        echo "カスタマコード ". $delete. " のアカウント状態を変更しました。";
                        $arr[] = null;

            $arr[] = null;                     
                    }          
                }
            } else {
                $cust = $_SESSION["cust"];
            }
        }
        
                
 
            if (isset($cust)) {   
                if ($cust["deleted_account"] == 0) {
                    $t = "利用中";
                    $m = "アカウント停止";                    
                } else {
                    $t = "停止中";
                    $m = "アカウント復旧";
                }
            ?>
    			<table>
            		<thead><tr><th>カスタマ連番</th><th>ユーザーID</th><th>ニックネーム</th><th>状態</th></tr></thead>               		
        			<tbody><tr>       			
   		<?php      
            echo "<td>". $cust["customer_code"]. "</td><td>". $cust["user_id"]. "</td><td>". $cust["nickname"]. "</td><td id='stopgo'><b>". $t. "</b></td>";       
            //二重送信防止
            $_SESSION["chkno"] = $chkno = mt_rand();
        ?>
    				</tr></tbody>   				
    			</table>
    			
        		<div class="btn1">
            		<form action="<?= $_SERVER["SCRIPT_NAME"] ?>" method="POST">
            		<input name="chkno" type="hidden" value="<?php echo $chkno; ?>">
                    <button id="button" type="submit" name="delete" value="<?= $cust["customer_code"] ?>"><?= $m ?></button>
                    </form>
        		</div>
    		<?php 
                } else {
                    echo "<br>コードを入力してください";
                }  
        }
            ?>
	     		
        		<footer>
        		    <br><br>
            		<a id="ikou" href="managermenu.php">管理メニューに戻る</a>
            		<br><br>	
            		<p id="copy">&copy;Picstock</p>		
        		</footer>
        	</main>
        </div>
        <script>	
         	$('#button').on('click', function() {
           
         		 var checked = window.confirm("アカウント状態を変更しますか？");
         	      if (checked == true) {
         	          return true;
         	      } else {
         	          return false;
         	      }
             	      	
        	});
        	$(window).ready(function(){
            	if($("#button").text() == "アカウント復旧"){
    				$("#button").css("background-color","rgb(0, 128, 255)");
                }else{
                	$("#button").css("background-color","rgb(255, 128, 128)");
                }
                if($('#stopgo').text() == "停止中"){
                	$("#stopgo").css("color","rgb(255, 0, 0)");
                }else{
                	$("#stopgo").css("color","rgb(0, 0, 255)");
                }
        	});

    	</script>
	</body>
</html>