$(function(){
  //Tab1以外を非表示にする
  $("#tab2").hide();
  $("#tab3").hide();

  //postされたときの判定
  
  $("#contents .tab").hide();
  let flg = $("input[name='table_flg']").val();
	switch (flg) {
	case 0:
		  $("#tab2").hide();
		  $("#tab3").hide();
	break;
	case 1:
		  $("#tab1").hide();
		  $("#tab3").hide();
	break;
	case 2:
		  $("#tab1").hide();
		  $("#tab2").hide();
	}
	
	$($('.a').eq(flg).attr("href")).show();
	//現在のcurrentクラスを削除
	$(".current").removeClass("current");
	//選択されたタブ（自分自身）にcurrentクラスを追加
	$('.a').eq(flg).addClass("current");

  //タブをクリック
  $(".a").click(function (){
    //一度全てのコンテンツを非表示
    $("#contents .tab").hide();

    //選択されたコンテンツを再表示
    $($(this).attr("href")).show();

    //現在のcurrentクラスを削除
    $(".current").removeClass("current");

    //選択されたタブ（自分自身）にcurrentクラスを追加
    $(this).addClass("current");
    
    return false;
  });

  $(".btn").click(function(){
    $('form').submit();
  });

  
});
