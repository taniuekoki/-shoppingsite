//分類日本語化リスト

const $genre_list = {"all":"すべての商品","animal":"動物","season":"季節","food":"食べ物","view":"風景"};


//タグ日本語化リスト

let $tag_list = null;

	//動物
	const $tag_animal = {"dog":"犬","cat":"猫","others":"その他"};
	
	//季節
	const $tag_season = {"spring":"春","summer":"夏","autumn":"秋","winter":"冬"};
	
	//食べ物
	const $tag_food = {"cooking":"料理","sweets":"お菓子","fruits":"フルーツ","others":"その他"};
	
	//風景
	const $tag_view = {"others":"その他"};
	
	/** 統合 **/
	$tag_list = {"animal":$tag_animal, "season":$tag_season,"food": $tag_food,"view": $tag_view , "alltag":"すべてのタグ","all":"すべて"};