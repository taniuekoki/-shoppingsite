<!DOCTYPE html>

<html lang="ja">
	<head>
	<meta charset="UTF-8">
	<title></title>
	<script src="./js/jquery-2.1.4.min.js"></script>
	</head>
	<body>
	<h1>Ajaxテスト</h1>
	3月1日10：30版
	
	
		<button type="button" id="pricebtn">値段100～300円</button>
		<script>
		$("#pricebtn").click(function(){
			console.log("100~300yen");

			$.ajax({
		        type: "POST",
		        url: "./ajax_itemlist.php",
		        data: {price_low: "100", price_high: "300"},

			}).done(function( data ) {
				
				console.log($.parseJSON(data));
			});

		});
		
		</script>
		
		
		<button type="button" id="genrebtn">ジャンル絞り込み</button>
		<script>
		$("#genrebtn").click(function(){
			console.log("genre:animal tag:dog");
			
			$.ajax({
		        type: "POST",
		        url: "./ajax_itemlist.php",
		        data: {item_genre: "animal", tag: "dog"},

			}).done(function( data ) {
				
				console.log($.parseJSON(data));
			});

		});
		
		</script>
		
				<button type="button" id="current_sale_btn">現在のセール</button>
		<script>
		$("#current_sale_btn").click(function(){
			console.log("current_sale");
			
			$.ajax({
		        type: "POST",
		        url: "./ajax_itemlist.php",
		        data: {current_sale: "1"},

			}).done(function( data ) {
				
				console.log($.parseJSON(data));
			});

		});
		
		</script>
		
		
		<button type="button" id="all_btn">全品</button>
		<script>
		$("#all_btn").click(function(){
			console.log("current_sale");
			
			$.ajax({
		        type: "POST",
		        url: "./ajax_itemlist.php",
		        data: {all_item: "1"},

			}).done(function( data ) {
				
				console.log($.parseJSON(data));
			});

		});
		
		</script>
		<br><br>
		
		
		
		
		
		
		<h2>実装用</h2>
		<button type="button" id="item_check_btn">対象商品を確認</button>
		<script>
		$("#item_check_btn").click(function(){

			//以前表示した中身を消去
			$(".item_list_dsp").empty();

			//ajax開始
			$.ajax({
		        type: "POST",
		        url: "./ajax_itemlist.php",
		        data: {all_item: "1"},//←ここにほしいデータを入れてく。上記参考

			}).done(function( data ) {
				
				console.log($.parseJSON(data));//あとで消す！

				item_list_data = $.parseJSON(data);
				for (let i=0; i<item_list_data.length; i++) {
					$(".item_list_dsp").append("<p>" + item_list_data[i]["item_name"] + "</p>");
				}
			});

		});
		
		</script>
		<div class="item_list_dsp"></div>
		
	</body>
</html>