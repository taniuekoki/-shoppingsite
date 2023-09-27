$(function(){
  //2023-03-08 fujimoto 16:30修正

	//-------タグ検索-----------
	//変更時に再読み込み
	const array = new Array();
	array["all"] = [
	  {cd:"alltag", label:"先にカテゴリを選んでください"}];
	array["animal"] = [
//	  {cd:"all", label:"すべて"},
      {cd:"dog", label:"犬"},
      {cd:"cat", label:"猫"},
      {cd:"others", label:"その他"},
    ];
    array["season"] = [
//	  {cd:"all", label:"すべて"},
      {cd:"spring", label:"春"},
      {cd:"summer", label:"夏"},
      {cd:"autumn", label:"秋"},
      {cd:"winter", label:"冬"},
    ];
    array["food"] = [
//	  {cd:"all", label:"すべて"},
      {cd:"cooking", label:"料理"},
      {cd:"sweets", label:"お菓子"},
      {cd:"fruits", label:"フルーツ"},
      {cd:"others", label:"その他"},
    ];
    array["view"] = [
//	  {cd:"all", label:"すべて"},
	  {cd:"others", label:"その他"}
	 ];
/*    array["others"] = [
      {cd:"all", label:"すべて"},
    ];*/
    
  //ジャンルが変更された時にタグを再構成
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
    
  //編集開始時に商品のジャンル・タグを設定
	$(window).ready( function() {

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
	});
    
  // jQuery Upload Thumbs起動用 
  $('form input:file').uploadThumbs();





});
