$(function(){
	const sale_flag = $('input:hidden[name="sale_flag"]').val();
	if (sale_flag == 1){
		$("#price").css("text-decoration", "line-through #ff0000");
	}
});