$(function() {
	
	// 初期状態のボタンは無効
	$("#btn1").prop("disabled", true).css("color","#888888").css("background","#ffffff").css("border","3px solid #888888");
    
	// チェックボックスの状態が変わったら（クリックされたら）
	$("input[type='checkbox']").on('change', function () {
		// チェックされているチェックボックスの数
		if ($("#ch2" ).prop('checked')) {
			// ボタン有効
			$("#btn1").prop("disabled", false).css("background","#888888").css("color","#ffffff");
		} else {
			// ボタン無効
			$("#btn1").prop("disabled", true).css("color","#888888").css("background","#ffffff").css("border","3px solid #888888");
		}
	});
	
});