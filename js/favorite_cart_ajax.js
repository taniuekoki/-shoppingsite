$(function(){
	//favorite.php用
	$(".cart_button_class").click(function(){
		let item_check = $(this).val();
		const history_flag = $('input:hidden[name="history_flag"]'+"."+item_check).val();
		  if(history_flag == 1){
			  $("#errmsg"+item_check).text("この商品は購入済みです");
		  }else{
			const item_code = $('input:hidden[name="item_code"]'+"."+item_check).val();
			const item_name = $('input:hidden[name="item_name"]'+"."+item_check).val();
			const price = $('input:hidden[name="price"]'+"."+item_check).val();
			const sale_flag = $('input:hidden[name="sale_flag"]'+"."+item_check).val();
			const seller_date = $('input:hidden[name="seller_date"]'+"."+item_check).val();
			const thumbnail = $('input:hidden[name="thumbnail"]'+"."+item_check).val();
			const customer_code = $('input:hidden[name="customer_code"]'+"."+item_check).val();
			const sale_ratio = $('input:hidden[name="sale_ratio"]'+"."+item_check).val();
			const campaign_name = $('input:hidden[name="campaign_name"]'+"."+item_check).val();
			const sale_price = $('input:hidden[name="sale_price"]'+"."+item_check).val();
			const sale_limit = $('input:hidden[name="sale_limit"]'+"."+item_check).val();
			
			$.post("./module/cart_in.php",{
				item_code: item_code,
				item_name: item_name,
				price : price,
				sale_flag : sale_flag,
				sale_ratio : sale_ratio,
				sale_price : sale_price,
				campaign_name : campaign_name,
				seller_date : seller_date,
				thumbnail : thumbnail,
				customer_code : customer_code,
				sale_limit:sale_limit
				},
				  function(data){
					     if(data.errmsg){
					     	$("#errmsg"+item_check).text(data.errmsg);
					     	$("#cart_in_success"+item_check).text("");
					     }else{
						     $("#cart_in_success"+item_check).text("カートに追加しました");
					     }
					     
					     $("#cart").html("<img id='cart' src='images/cart_icon/cart"+data.cart_count+".png' alt='カート'>");
				},"json"
			);
		  }
	  });
});