<?php
session_start();


include './module/connect.php';

$login_userdata = $_POST['login_userdata'];
$fav_item_code = $_POST['fav_item_code'];
 
// 直接やりたいのでこうしてます
//  $item_code = 10010;
//  $customer_code = 10100;

// echo $login_userdata. "<br>". $fav_item_code;



//データベースから、$customer_codeでfav_item を $fav_itemに入れる
$db1 = "select fav_item from k2g2_customer where customer_code = ? " ;    //実行するsql文を入力"
$arr = array();
$arr[] = $login_userdata;

$result = db_execution($db1, $arr);
$fav = $result->fetch();

$fav_arr = explode(',', $fav["fav_item"]); //カンマ区切り文を配列に変換する

if(!in_array($fav_item_code, $fav_arr)) {   //お気に入りリストに item_codeと同じものがあるか検証{無かったら追加、有ったら削除}

    array_unshift($fav_arr, $fav_item_code); //配列の前に追加する
    
} else {
        $res = array_search($fav_item_code, $fav_arr);//配列から削除する番号を指定
        unset($fav_arr["$res"]);  //指定した番号で削除
    
}
    $fav_item = implode(",", $fav_arr); //カンマ区切りで、文字列に入れる。
    array_unshift($arr, $fav_item);
    $db2 = "update k2g2_customer set fav_item = ? where customer_code = ?";
    $result = db_execution($db2, $arr);
    
    //*******商品のお気に入り数更新
    $db->beginTransaction();
    
    $arr_cup = array();
    $arr_cup[] = $fav_item_code;
    $sql = "select favorite from k2g2_item where item_code = ?";
    $result = db_transaction($sql, $arr_cup);
    $fav_count = $result->fetch();
    
    if (in_array($fav_item_code, $fav_arr)) {
    	$fav_count[0]++;
    }else{
    	$fav_count[0]--;
    }
    
    array_unshift($arr_cup, $fav_count[0]); //配列の先頭に追加する
    
    $sql_cup = "update k2g2_item set favorite = ? where item_code = ?";
    $result_cup = db_transaction($sql_cup, $arr_cup);
    
    $db->commit();
    //*******
    
   if($result && $result_cup){
       echo "1";//ajaxで返すやつ
         //echo "完了";
   }else{
       echo "0";//ajaxで返すやつ
        //echo "失敗";
   }
        
    
//}else{
//     echo "入ってましたよアップデート処理スキップ";
//  echo "1";//ajaxで返すやつ
//}



    
    
// for($i = 0; $i < count($okini); $i++) {
//     echo $okini[$i];
// }


//fav_item

//$customer_code;
//$item_code;

//echo json_encode(array("返した"));
// echo json_encode(array("cart_count"=>$cart_count,"errmsg"=>$errmsg,JSON_UNESCAPED_UNICODE));
?>