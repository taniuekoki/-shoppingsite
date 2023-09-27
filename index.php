<?php
//2023_02_13 レスポンシブ対応　喜多
//2023_02_06 ヘッダー修正済み　/小林
//2023 02-06 14:54 新着順追加（タグランダム）　谷上
//2023 02-06 15:29 sql文変更（非公開・削除対策）谷上
//2023 02-10 09:51 利用概要追加　藤本
//2023-03-07 11:00 fujimoto titleタグ直し・nobanner処理

session_start();
include ("./module/connect.php");
?>
<!doctype html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Picstock</title>
  <link rel="stylesheet" href="css/reset.css">  
  <link rel="stylesheet" href="slick/slick.css">
  <link rel="stylesheet" href="slick/slick-theme.css">
  <link rel="stylesheet" href="css/index.css">
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="slick/slick.min.js"></script>
  <script src="js/index.js"></script>
</head>

<body>
	<div class="innerWrap">
		<header class="header">
	    	<?php include "header.php" ?>
	    </header>
	</div>
	<main>
    <div class="innerWrap">
      <?php 
      
      $arr=array();
      $sql="select * from k2g2_discount";
      
      $reslut = db_execution($sql, $arr);
      
      if ($drow=$reslut->fetch()) {
          ?><a class='banana' href="./search.php?sale=1"><img src="./images/campaigns/<?=$drow["sale_banner"]?>"></a><?php 
      }else{
        ?><div class="nobanner"></div><?php
      }
      ?>
      
		<div class="topslider">





            <?php 
            //15商品づつ表示
            for($i=0;$i < 3;$i++){
                $arr=array();
                $sql="select * from k2g2_item  where sell_enable = 1 &&  deleted_item=0 order by rand() limit 15";
                
                $reslut = db_execution($sql, $arr);
            ?>
            <div class="photo">
	        	<section class="subPage">
	           		<ul class="photoGarelly">
	               		<?php
	                     $j=1;
	                     while ($row=$reslut->fetch()){
	                     ?>
	                	<li class="item<?php
	                       if($j<10){
	                           echo "0".$j;
	                       }else{
	                           echo $j;
	                       }
	                    ?>"> 
	                    <a href="item.php?item_code=<?= $row["item_code"] ?>"><img src="images/thumbnails/owners/<?=$row["customer_code"] ?>/<?=$row["thumbnail"] ?>" name="<?= $row["item_code"] ?>" alt="<?= $row["item_name"] ?>"></a>
	                    </li>
	                  	<?php $j++; }?>
	             	</ul>

                     <div class="slider_logo">
            <img src="images/top2.png" alt="ロゴ">
            <p>高品質な写真素材・イラスト素材が、低価格で購入できます。商用利用もOK!!!</p>
        </div>






	          	</section>
	   		</div>
<?php } ?>
		</div>


        


<?php 
//新着・人気表示
$tag_list = ["","&& item_genre = 'animal'","&& item_genre = 'season'","&& item_genre = 'food'","&& item_genre = 'view'"];	//新着用配列

        for ($i = 0; $i < 2; $i++) {
            if($i){
                $title="人気";
                $clumn="favorite";
                $sql="select * from k2g2_item where  sell_enable = 1 &&  deleted_item = 0 order by ".$clumn." desc limit 10";
                
            }else{
                $title="新着";
                $clumn="seller_date";
                $sql="select * from k2g2_item where  sell_enable = 1 &&  deleted_item = 0 ".$tag_list[rand(0,4)]." order by ".$clumn." desc limit 10";
                
            }
            $arr=array();
            $reslut = db_execution($sql, $arr);
?>
          <h3><?=$title?></h3>
         
   		<div class="center slider sliimg">
<?php 
    $j=1;
    while ($row=$reslut->fetch()){?>
  			<div><span class="juni"><?php if($i){echo $j."位";}?></span><a href="item.php?item_code=<?= $row["item_code"] ?>" style="border-radius:10px;"><img src="images/thumbnails/owners/99999/<?=$row["thumbnail"] ?>" alt="" class="radius10"></a></div>
<?php $j++;}?>
		</div>
<?php }?>
    </div>
    </main>
	<div class="innerWrap">
	<p id="license_title">Picstock利用概要</p>
	<textarea id="license_content" >【会員登録】
・当サイトを利用するためには、会員登録が必要です。
・会員は、ID及びパスワードを第三者に利用させたり、貸与、譲渡、売買、担保提供等をすることはできないものとします。
・会員が、利用規約等に違反した又はその恐れがあると弊サイトが認めた場合には、当該会員に通知することなく、本サービスの全部又は一部の利用を停止することができるものとし、それによって当該会員が被った一切の損害について、弊社は責任を負わないものとします。

【有料コンテンツサービス】
※当サイトは実習課題のデモサイトのため、機能デモとして支払い機能を有するものであって、実際に金銭のやりとりは発生しません。

【写真のダウンロードと使用】
※当サイトは実習課題のデモサイトのため、実際に素材のダウンロードは可能ですが、当該画像の再配布、二次利用は禁止とさせていただきます。</textarea>
	
		<footer>
			<?php include 'footer.php';?>
		</footer>
    </div>
  </body>
</html>