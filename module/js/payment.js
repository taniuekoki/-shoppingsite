
//2023-02-22 kita 
//2023-02-08 fujimoto 警告文チェック追加

$(function(){
	
	let windowWidth = $(window).width();

const windowSm = 640;

if (windowWidth >= windowSm) {
	//横幅640px以上（パソコン）に適用させるJavaScriptを記述

	//ブラウザをスクロール
	$(window).scroll(function(){
		//ナビゲーションの新しい位置へ移動
		$(".pay").stop().animate({"top" : $(window).scrollTop() + 100}, 300);
	});
	
	//---navここから-----
	
	
	//警告文のチェック
	$("#payoutbtn").click(function(){
		
		if($("#paychkbox").prop("checked")){
			//チェックされてる
			return true;
		}else{
			//同意されてない
			alert("同意してください");
			return false;
		}
	});
	}
	
	
});