<?php
//タグ分類リスト
//最終更新 2023-01-29 fujimoto
/**
*
*	使い方
*	・ジャンルとタグで変数は分離しています
*	
*	・$genre_list["animal"]といれると「動物」が返ります。
*
*	・$tag_list["dog"] と入れると、「犬」が返ります
*	・$tag_listにはすべてのジャンルのタグが入っています。
*	・使用する際は問題なければ$tag_listを使ってください。
*
**/

//分類日本語化リスト

$genre_list = ["all"=>"すべての商品","animal"=>"動物","season"=>"季節","food"=>"食べ物","view"=>"風景"];


//タグ日本語化リスト

$tag_list = null;

	//動物
	$tag_animal = ["dog"=>"犬","cat"=>"猫","others"=>"その他"];
	
	//季節
	$tag_season = ["spring"=>"春","summer"=>"夏","autumn"=>"秋","winter"=>"冬"];
	
	//食べ物
	$tag_food = ["cooking"=>"料理","sweets"=>"お菓子","fruits"=>"フルーツ","others"=>"その他"];
	
	//風景
	$tag_view = ["others"=>"その他"];
	
	
	/** 統合 **/
	$tag_list = $tag_animal + $tag_season + $tag_food + $tag_view + array("alltag"=>"すべてのタグ","all"=>"すべて");