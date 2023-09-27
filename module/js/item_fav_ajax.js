//お気に入り用
 $(function(){
	const fav_check = $('input:hidden[name="fav_check"]').val();
	const fav_item_code = $('input:hidden[name="fav_item_code"]').val();
	const login_userdata = $('input:hidden[name="login_userdata"]').val();
	
	
		if (fav_check == 0) {
		 	$(".like_off").hide();
		} else if (fav_check == 1) {
			$(".like_on").hide();
		} else {
			$(".like_off").hide();
			$(".like_on").hide();
		}
	
		$("#like").on('click',function(){	
			//console.log("お気に入ボタンをおしました");
//			console.log(login_userdata);
//			console.log(fav_item_code);
			$.post("./favin.php",
//		$.post("./test.php",
			{
				"fav_item_code" : fav_item_code,				
				"login_userdata" : login_userdata

			}).done(function(data) {  
				if (data == "1") {
					$("#like_in").text("お気に入りに追加しました");
					$(".like_on").hide();
					$(".like_off").show();
					$fav_check = 1;
				} else {
					$("#like_in").text("お気に入りの追加に失敗しました。");
				}
				
				
       		}).fail(function(XMLHttpRequest, status, e){
       			$("#like_in").text("お気に入りに追加失敗しました。"); 
    		});
		});

		
		$("#notlike").on('click',function(){
				$(".like_off").show();				
					 
    			//console.log("お気に入り解除ボタンをおしました");
    			$.post("./favin.php",
    			{
    				"fav_item_code" : fav_item_code,				
    				"login_userdata" : login_userdata
    			}).done(function(data) {  
   				if (data == "1") {
    				$("#like_in").text("お気に入りから削除しました");
    				$(".like_off").hide();
    				$(".like_on").show();
    				$fav_check = 0;
   				} else {
						$("#like_in").text("お気に入りからの削除に失敗しました。");
				}
    				
           		}).fail(function(XMLHttpRequest, status, e){
           			$("#like_in").text("お気に入りからの削除に失敗しました。");
        		});	     
			
		});
	});







