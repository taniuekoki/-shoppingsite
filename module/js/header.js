$(function(){
	//1-25 17:00 taniu
	//2023-1-29 19:00 fujimoto 5～30行 名前の修正
	//-------タグ検索-----------

	
	//変更時に再読み込み
	const array = new Array();
	array["all"] = [
	  {cd:"alltag", label:"すべてのタグ"}];
	array["animal"] = [
	  {cd:"all", label:"すべて"},
      {cd:"dog", label:"犬"},
      {cd:"cat", label:"猫"},
      {cd:"others", label:"その他"},
    ];
    array["season"] = [
	  {cd:"all", label:"すべて"},
      {cd:"spring", label:"春"},
      {cd:"summer", label:"夏"},
      {cd:"autumn", label:"秋"},
      {cd:"winter", label:"冬"},
    ];
    array["food"] = [
	  {cd:"all", label:"すべて"},
      {cd:"cooking", label:"料理"},
      {cd:"sweets", label:"お菓子"},
      {cd:"fruits", label:"フルーツ"},
      {cd:"others", label:"その他"},
    ];
    array["view"] = [
	  {cd:"all", label:"すべて"},
	  {cd:"others", label:"その他"}
	 ];
/*    array["others"] = [
      {cd:"all", label:"すべて"},
    ];*/
    
    document.getElementById('menu1').onchange = function(){
      menu2 = document.getElementById("menu2");
      menu2.options.length = 0
      const changed = menu1.value;
      for (let i = 0; i < array[changed].length; i++) {
        const op = document.createElement("option");
        value = array[changed][i];
        op.value = value.cd;
        op.text = value.label;
        menu2.appendChild(op);
      }
    }
    
    //ページ読み込み時に設定
    $(window).ready(function(){
		if(form_select == "genre"){
			$('.text').hide();
    		$('.text02').show();
    		$(".search button[type=submit]").css("border-radius","5px");
    		$('[id=b]').prop('checked', true);
    	}
	});
	$(window).load( function() {
		if(form_select == "genre"){
			$("#menu1").val(form_genre);
			const menu1def = $('#menu1 option:selected').val();

			$("#menu2").children().remove();
			for (let i = 0; i < array[menu1def].length; i++) {
	        	const op = document.createElement("option");
		        value = array[menu1def][i];
		        op.value = value.cd;
		        op.text = value.label;
		        menu2.appendChild(op);
	        }
	        
	        
	        $("#menu2").val(form_tag);
        }
	});
    
    //-----radioボタンのチェック-----
/*    
    //戻るなどの時にradioボタンリセットする
    $(window).load( function() {
		$('[id=a]').prop('checked', true);
	});
*/
    
    $('[name="kensaku"]:radio').change( function() {
    	if($('[id=a]').prop('checked')){
    		$('.text').hide();
    		$('.text01').show();
    		$(".search button[type=submit]").css("border-radius"," 0 5px 5px 0");
    	} else if ($('[id=b]').prop('checked')) {
			$(".search button[type=submit]").css("border-radius","5px");
    		$('.text').hide();
    		$('.text02').show();
    	}
    });
    
    //-----radioボタンのチェックここまで-----
    
    

    
    
	//-------タグ検索-----------

  let windowWidth = $(window).width();

	const windowSm = 640;

  //  ----------navここから---------------

	
	 if (windowWidth <= windowSm) {
    //リストメニュー追加
    const content = document.querySelector('#uull');

    content.insertAdjacentHTML('afterbegin','<li><a href="cart.php">カート</a></li>');
    content.insertAdjacentHTML('afterbegin','<li><a href="favorite.php">お気に入り</a></li>');
    content.insertAdjacentHTML('afterbegin','<li><a href="index.php">ホーム</a></li>');
    content.insertAdjacentHTML('afterbegin','<li><a href="login.php">ログイン</a></li>');
    content.insertAdjacentHTML('afterbegin','<li><a href="register.php">新規登録</a></li>');




   }else{

    let nav = $("#nav").offset().top;
    $(window).on("scroll", function() {
      // スクロール値を取得する場合「.scrollTop()」
      let scroll = $(window).scrollTop();
      let fixd = 90-scroll;
      if(fixd <= 0){
        $("#nav").css("position","fixed");
        // $('.slider_logo').css("margin-top","81px");
        $("#space").css("height","81px");
        /*$("h2").css("margin-top","100px");*/
      }else{
        $("#nav").css("position","");
        // $('.slider_logo').css("margin-top","0px");
        $("#space").css("height","0px");
      /* $("h2").css("margin-top","");*/

      }
    });
  }
    // -----------navここまで---------------

  // 
  // ハンバーガーメニュークリックイベント
  $('.hamburger-menu').click(function(){
    if($(this).hasClass('open')){
      // ナビゲーション非表示
      $('.nav-sp').slideToggle(200);
      // ハンバーガーメニューを元に戻す
      $(this).removeClass('open');
    }else{
      // ナビゲーションを表示
      $('.nav-sp').slideToggle(200);
      // ハンバーガーメニューを✖印に変更
      $(this).addClass('open');
    }
  });

  // ---------top画像集ここから-----------

  //スクロールの方向 -1の時は左、1の時には右
  let dir = -1;

  //スクロールのインターバル（何秒ごとにスクロールさせるか。3000ミリに設定）
  const interval = 3000;

  //スクロールのスピード（700ミリ秒に設定）
  const duration = 700;

  //タイマー用の変数
  let timer;

  //リストの順番を変更（３番目を１番最初にする）
  $("#slide .ul").prepend($("#slide photo:last-child"));

  //リストの位置を変更（画像１枚分ずらす）
  $("#slide .ul").css("left", -1200);

  //3000ミリ秒（変数intervalの値）ごとにslideTimer()関数を実行
  timer = setInterval(slideTimer, interval);

  //slideTimer()関数
  function slideTimer(){
    if(dir == -1){
      //画像一枚分左へスクロール
      $("#slide .ul").animate({"left" : "-=1200px"}, duration,
      function(){
        //リストの順番を変更
        $(this).append($("#slide photo:first-child"));

        //リストの位置を変更
        $(this).css("left", -1200);
      });
    }else{
      //画像一枚分右へスクロール
      $("#slide .ul").animate({"left" : "+=1200px"}, duration,
      function(){
        //リストの順番を変更
        $(this).prepend($("#slide photo:last-child"));

        //リストの位置を変更
        $(this).css("left", -1000);

        //左方向へリセット
        dir = -1;

    });
  }
}

  //前へ戻るボタン
  $("#prevBtn").click(function(){
    //スクロール方向の切り替え（右）
    dir = 1;

    //タイマーを停止して再スタート
    clearInterval(timer);
    timer = setInterval(slideTimer, interval);

    //初回の関数実行
    slideTimer();
  });

  //次へ進むボタン
  $("#nextBtn").click(function(){
    //スクロール方向の切り替え（左）
    dir = -1;

    //タイマーを停止して再スタート
    clearInterval(timer);
    timer = setInterval(slideTimer, interval);

    //初回の関数実行
    slideTimer();
  });

  // ---------top画像集ここまで-----------


});