//2023-02-07 13:00 console消し修正fujimoto

$(function(){
	 //item.php用
	$("#cart_button").click(function(){
		const history_flag = $('input:hidden[name="history_flag"]').val();
		  if(history_flag == 1){
			  $("#errmsg").text("この商品は購入済みです");
		  }else{
			const item_code = $('input:hidden[name="item_code"]').val();
			const item_name = $('input:hidden[name="item_name"]').val();
			const price = $('input:hidden[name="price"]').val();
			const sale_flag = $('input:hidden[name="sale_flag"]').val();
			const seller_date = $('input:hidden[name="seller_date"]').val();
			const thumbnail = $('input:hidden[name="thumbnail"]').val();
			const customer_code = $('input:hidden[name="customer_code"]').val();
			const sale_ratio = $('input:hidden[name="sale_ratio"]').val();
			const campaign_name = $('input:hidden[name="campaign_name"]').val();
			const sale_price = $('input:hidden[name="sale_price"]').val();
			const sale_limit = $('input:hidden[name="sale_limit"]').val();
			
			
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
					     //console.log("セッションカート数："+data.cart_count);
					     if(data.errmsg){
					     	$("#errmsg").text(data.errmsg);
					     	$("#cart_in_success").text("");
					     }else{
						     $("#cart_in_success").text("カートに追加しました");
					     }
					     //console.log(sale_limit);
					     $("#cart").html("<img id='cart' src='images/cart_icon/cart"+data.cart_count+".png' alt='カート'>");
				},"json"
			);
		  }
	  });
	
});