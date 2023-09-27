<?php
$genre=["animal","season","food","view"];
$tag=[
		"animal"=>["dog","cat","others"],
		"season"=>["spring","summer","autumn","winter"],
		"food"=>["cooking","sweets","fruits","others"],
		"view"=>["others"]
];
$max_value=$k=0;
$glf_value=$_SESSION["category_arr"]=array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST["month_next"])){
		$month = $_POST["month_next"]+1;
		//翌年の１月にする処理
		if ($month == 13) {
			$month = 1;
			//セッションから年を取得し、インクリメントして変数に保存
			$year_category = $_SESSION["year_category"];
			$year_category++;
			$_SESSION["year_category"] =$year_category;
		}
	}else{
		if(isset($_POST["month_back"])){
			$month = $_POST["month_back"]-1;
			//昨年の１２月にする処理
			if ($month == 0) {
				$month = 12;
				//セッションから年を取得し、デクリメントして変数に保存
				$year_category = $_SESSION["year_category"];
				$year_category--;
				$_SESSION["year_category"] =$year_category;
			}
		}else{
			//このグラフ以外でPOSTされた時にバグるので対策
			$year_category = date("Y");
			$_SESSION["year_category"] = $year_category;
			$month = date("n");
		}
	}
	$year_category = $_SESSION["year_category"];
}else{
	////初回は現在の年月を取得
	$year_category = date("Y");
	$_SESSION["year_category"] = $year_category;
	$month = date("n");
}
$_SESSION["category_arr"][$k++][] = $year_category."年".$month."月";
$_SESSION["category_arr"][$k][] = "ジャンル";
$_SESSION["category_arr"][$k][] = "タグ";
$_SESSION["category_arr"][$k++][] = "金額(円)";

/*-------------------グラフの最高値計算↓---------------------------*/
foreach ($genre as $genre_value) {
	$total_value = 0;
	foreach ($tag[$genre_value] as $tag_value) {
		$_SESSION["category_arr"][$k][] = $genre_list[$genre_value];	//ｃｓｖ用
		$_SESSION["category_arr"][$k][] = $tag_list[$tag_value];	//ｃｓｖ用
		//ひと月のタグの売上取得
		$arr = array();
		$sql = "select sum(k2g2_sales.sold_price) from k2g2_sales inner join 
k2g2_item on k2g2_sales.item_code = k2g2_item.item_code and 
k2g2_item.tag = '".$tag_value."' and k2g2_item.item_genre = '".$genre_value."' and
 sold_date between '".$year_category."-".$month."-01' and '".$year_category."-".$month."-31 23:59:59'";
		$result = db_execution($sql,$arr);
		$value = $result->fetch();
		
		if (isset($value[0])) {
			$total_value += $value[0];
			//表示用に配列に保存
			$glf_value[$genre_value][$tag_value] = $value[0];
			$_SESSION["category_arr"][$k][] = $value[0];	//ｃｓｖ用
		}else{
			//表示用に配列に保存
			$glf_value[$genre_value][$tag_value] = null;
			$_SESSION["category_arr"][$k][] = 0;	//ｃｓｖ用
		}
		$k++;
	}
	$_SESSION["category_arr"][$k][] = "";
	$_SESSION["category_arr"][$k][] =  "合計";
	$_SESSION["category_arr"][$k++][] = $total_value;
	$_SESSION["category_arr"][$k][] = null;
	$k++;
	
	if($max_value < $total_value){
		$max_value = $total_value;
	}
}

$round = (strlen($max_value) - 1);
//四捨五入した時に切り上げになるように値に+5000や+500する値を生成
for ($i=0,$j=1;$i<$round-1;$i++,$j *= 10);
$max_value += $j * 5;

$round *= -1;
$scale = round($max_value,$round);
/*-------------------グラフの最高値計算↑---------------------------*/
?>
<div class="year_category_wrap">
 <p id="year_category"><?=$year_category?>年　<?=$month?>月</p>
</div>
<div class="category_table_wrap">
<table id="stacked-example-3" class="charts-css column show-heading show-labels show-primary-axis show-3-secondary-axes data-spacing-10 multiple stacked">
  <tbody>
<?php 
//ジャンル分ループ
foreach ($genre as $genre_value) {
?>
    <tr>
      <th scope="row"><?=$genre_list[$genre_value]?></th>
      <?php 
      //タグ分ループ
      foreach ($tag[$genre_value] as $tag_value){
      	if ($glf_value[$genre_value][$tag_value]) {
      ?>
      <td class="td" style="--size:calc(<?=$glf_value[$genre_value][$tag_value]?> / <?=$scale?>);"><span class="data"> <?=$tag_list[$tag_value]?>(<?=$glf_value[$genre_value][$tag_value]?>円) </span></td>
      <?php }}?>
    </tr>
<?php 
}
?>
  </tbody>
</table>
</div>
<div class="nbb">
	<span class="next_back_btn">
		<button class="btn" name="month_back" value="<?=$month ?>">前月</button>
		&emsp;&emsp;&emsp;&emsp;<button class="btn" name="month_next" value="<?=$month ?>">次月</button>
	</span>
</div>