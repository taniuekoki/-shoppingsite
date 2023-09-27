$(function(){
//2023-02-21 13:00 fujimoto 初期制作

    //バリデーション
    $("#login_form").validate({
        //ルール
        rules: {
            user_id: {
              required: true, 
              email: true,
            },
            user_pass: {
              required: true, 
            },
        },

        // エラーメッセージ
        messages: {
            user_id: {
                required: 'ID(メールアドレス)を入力してください',
                email: 'メールアドレスの形式で入力してください',
            },
            user_pass: {
                required: 'パスワードを入力してください',
            },
        },
    
        // エラーメッセージ出力箇所
        errorPlacement: function(error, element){
            $(".err_msg").text("");
            $(".is-error-"+name).text("");
            var name = element.attr('name');
            error.appendTo($('.is-error-'+name));
        },
        errorElement: "span",
        errorClass: "is-error",
    });

    $("#mfacode_form").validate({
        //ルール
        rules: {
            mfacode: {
              required: true, 
              digits: true,
              minlength: 6,
              maxlength: 6,
            },
        },

        // エラーメッセージ
        messages: {
            mfacode: {
                required: '認証番号を入力してください',
                digits: '正しく入力してください',
                minlength: '文字数が異なります',
                maxlength: '文字数が異なります',
            },
        },
    
        // エラーメッセージ出力箇所
        errorPlacement: function(error, element){
            $(".err_msg").text("");
            $(".is-error-mfacode").text("");
            var name = element.attr('name');
            error.appendTo($('.is-error-'+name));
        },
        errorElement: "span",
        errorClass: "is-error",
    });









});