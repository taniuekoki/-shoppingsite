$(function(){
	
	$(window).ready(function(){
		$('table').each(function(){
			const val = $(this).find('input:hidden[name="sale_flag"]').val();
			if (val == 1){
				const ele = $(this).find(".price");
				$(ele).css("text-decoration", "line-through #ff0000");
			}
		});
	});
});