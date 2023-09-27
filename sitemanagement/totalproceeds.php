<?php 

$_SESSION["total_proceeds_arr"]=array();
$max_val=100;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST["year_next"])){
		$year = $_POST["year_next"]+1;
	}else{
		if(isset($_POST["year_back"])){
			$year = $_POST["year_back"]-1;
		}else{
			$year = date("Y");
		}
	}
}else{
	//初回は現在の年を取得
	$year = date("Y");
}

/*----------------------------------年の総売上用	↓------------------------------------*/
$arr = array();
$sql = "select sum(sold_price) from k2g2_sales where sold_date between '".$year.
"-01-01' and '".$year."-12-31'";
$result = db_execution($sql,$arr);
$total_sum = $result->fetch();
/*----------------------------------年の総売上用↑------------------------------------*/

/*----------------------------------横軸メモリ用↓------------------------------------*/
for ($i = 1; $i < 13; $i++) {
	$arr = array();
	$sql = "select sum(sold_price) from k2g2_sales where sold_date between '".$year.
	"-0".$i."-01' and '".$year."-".$i."-31'";
	$result = db_execution($sql,$arr);
	$value = $result->fetch();
	if ($value[0] >= $max_val) {
		$max_val = $value[0];
	}
}

$round = (strlen($max_val) - 1);
//四捨五入した時に切り上げになるように値に+5000や+500する値を生成
for ($i=0,$j=1;$i<$round-1;$i++,$j *= 10);
$max_val += $j * 5;

$round *= -1;
$scale = round($max_val,$round);
/*----------------------------------横軸メモリ用↑------------------------------------*/

?>
<div id="total_proceeds">
 <div class="scale">
	<p id="scale1"><?=$scale?></p>
	<p id="scale2"><?=$scale*0.75?></p>
	<p id="scale3"><?=$scale*0.5?></p>
	<p id="scale4"><?=$scale*0.25?></p>
 </div>
<div>
	<div class="title_wrap">
	 <p id="title"><?=$year ?>年<span>&emsp;&emsp;
	 総売上：<?php if(isset($total_sum[0])) echo $total_sum[0]; else echo "0"?>円</span></p>
	</div>
	<div class="table_wrap">
	<table id="line-example-1" class="charts-css line hide-data show-labels show-primary-axis show-4-secondary-axes show-data-axes">
		<tbody>
			<?php 
			for ($i = 1; $i < 13; $i++) {
				$arr = array();
				$sql = "select sum(sold_price) from k2g2_sales where sold_date between '".$year.
				"-0".$i."-01' and '".$year."-".$i."-31'";
				$result = db_execution($sql,$arr);
				$value = $result->fetch();
				
				//				　↓グラフの最大値
				$val = $value[0]/$scale;
				
				if($i==1){
					$j = $val;
				}
				?><tr>
				<th> <?=$i ?>月 </th>
				<td style="--start:<?=$j ?>; --size:<?=$val ?>;"><span class="value">
				<?php 
				//csv出力用セッション
				$_SESSION["total_proceeds_arr"][0][] = $i."月";
				
				if ($value[0]) {
					echo "\\".$value[0];
					//csv出力用セッション
					$_SESSION["total_proceeds_arr"][1][] = $value[0];
				}else{
					//csv出力用セッション
					$_SESSION["total_proceeds_arr"][1][] = 0;
				}?></span></td>
				</tr><?php 
				$j = $val;
			}
			?>
		</tbody>
		</table><br>
		</div>
		</div>
	</div>
	<div class="nbb">
		<span class="next_back_btn">
			<button class="btn" name="year_back" value="<?=$year ?>">前年</button>
			&emsp;&emsp;&emsp;&emsp;<button class="btn" name="year_next" value="<?=$year ?>">次年</button>
		</span>
	</div>