<html lang="ja">
<head>
<meta charset="UTF-8">
<title></title>
</head>
<body>
<?php
include("./module/connect.php");

$arr=array();
$sql="select * from k2g2_discount";

$result=db_execution($sql,$arr);

if ($row=$result->fetch()) {
	echo "<img src='./images/campaigns/".$row["sale_banner"]."'>";
}


?>
</body>
</html>