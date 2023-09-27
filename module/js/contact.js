$(function(){
	
	//-------タグ検索-----------
	
	//変更時に再読み込み
	const arr = new Array();
	arr["none"] = [
	  {cd:"none", label:"---選択してください---"}];
	arr["item"] = [
	  {cd:"none", label:"---選択してください---"},
	  {cd:"ダウンロードできない", label:"ダウンロードできない"},
      {cd:"商品をキャンセルしたい", label:"商品をキャンセルしたい"},
    ];
    arr["system"] = [
	  {cd:"none", label:"---選択してください---"},
	  {cd:"購入できない", label:"購入できない"},
      {cd:"ページが開けない", label:"ページが開けない"},
    ];
    arr["account"] = [
	  {cd:"none", label:"---選択してください---"},
	  {cd:"ログインできない", label:"ログインできない"},
      {cd:"登録できない", label:"登録できない"},
    ];
    /*arr["others"] = [
	  {cd:"other", label:"すべて"}
	 ];
/*    array["others"] = [
      {cd:"all", label:"すべて"},
    ];*/
    
    document.getElementById('menu3').onchange = function(){
      menu4 = document.getElementById("menu4");
      menu4.options.length = 0
      const changed2 = menu3.value;
      for (let i = 0; i < arr[changed2].length; i++) {
        const op2 = document.createElement("option");
        value = arr[changed2][i];
        op2.value = value.cd;
        op2.text = value.label;
        menu4.appendChild(op2);
      }
    }
    
    //ページ読み込み時に設定
    /*$(window).ready(function(){
		if(form_select == "genre"){
			$('.text').hide();
    		$('.text02').show();
    		$('[id=b]').prop('checked', true);
    	}
	});
	$(window).load( function() {
		if(form_select == "genre"){
    		console.log("ジャンル検索");
			console.log(form_genre);
			console.log(form_tag);
			$("#menu3").val(form_genre);
			const menu1def = $('#menu3 option:selected').val();

			$("#menu4").children().remove();
			for (let i = 0; i < array[menu1def].length; i++) {
	        	const op = document.createElement("option");
		        value = array[menu3def][i];
		        op.value = value.cd;
		        op.text = value.label;
		        menu4.appendChild(op);
	        }
	        
	        
	        $("#menu4").val(form_tag);
        }
	});*/
	
	
	
	//アラートを全て非表示
	$(".alert").hide();
		
	//送信ボタンをクリック
	$("#conbtn").click(function(){
		let sendflag = true;
		
		//メールアドレスが未入力の場合
		if(!$("#email").val()){
			//メールのところのアラート表示
			$("#mailbox .alert").show();
			sendflag = false;
			
		}else{
			
			//メールアドレスが入力されていれる
			
			//メールの値を取得
			let email = document.getElementById('email');
			let val = email.value;
			//メールアドレスの正規表現
			const regex = /^[a-zA-Z0-9_+-]+(\.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/;
	
			//正規表現チェック
			if(!regex.test(val)){
				$("#mailbox .alert").show();
				sendflag = false;
			}else{
				$("#mailbox .alert").hide();
				
			}
		}
		
		let menu3 = document.getElementById("menu3").value;
		if(menu3 == "none"){
			//選択のところのアラート表示
			$("#selectbox1 .alert").show();
			sendflag = false;
		}else{
			$("#selectbox1 .alert").hide();
		}
		let menu4 = document.getElementById("menu4").value;
		if(menu4 == "none"){
			//選択のところのアラート表示
			$("#selectbox2 .alert").show();
			sendflag = false;
		}else{
			$("#selectbox2 .alert").hide();
		}
		
		
		//変数sendflagの値をチェック
		if(sendflag == false){
			return false;  //falseであれば、送信を許可しない
		}else{
			//trueだったらsubmitさせる
			document.conform.cate2.value = menu4;
			
			$('#conform').submit();
		}
	
	});

 });