$(function () {
  // jQuery Upload Thumbs 
  $('form input:file').uploadThumbs();

  const array = new Array();
  array["all"] = [
    { cd: "all", label: "すべてのタグ" }];
  array["animal"] = [
    { cd: "all", label: "すべて" },
    { cd: "dog", label: "犬" },
    { cd: "cat", label: "猫" },
    { cd: "others", label: "その他" },
  ];
  array["season"] = [
    { cd: "all", label: "すべて" },
    { cd: "spring", label: "春" },
    { cd: "summer", label: "夏" },
    { cd: "autumn", label: "秋" },
    { cd: "winter", label: "冬" },
  ];
  array["food"] = [
    { cd: "all", label: "すべて" },
    { cd: "cooking", label: "料理" },
    { cd: "sweets", label: "お菓子" },
    { cd: "fruits", label: "フルーツ" },
    { cd: "others", label: "その他" },
  ];
  array["view"] = [
    { cd: "all", label: "すべて" },
    { cd: "others", label: "その他" }
  ];

  document.getElementById('menu1').onchange = function () {
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

  //ラジオボタン絞り込み用
  $(".item_select_wrap").hide();
  $("input:radio[name='item_select']").click(function () {
    if ($(this).attr('id') == "sel_choice") {
      $(".item_select_wrap").show();
    } else {
      $(".item_select_wrap").hide();
    }
  });


  let item_list_click_flg = false;
  $("#item_check_btn").click(function () {
    item_list_check();
    item_list_click_flg = true;
    return false;
  });

  $("#cam_ratio").change(function () {
    if (item_list_click_flg) {
      item_list_check();
    }
  });

  // //絞り込み変更時にフラグやめる
  $("input:radio[name='item_select']").change(function(){
    item_list_click_flg = false;
  });


  //キャンペーン対象商品確認用ajax
  function item_list_check() {
    let $select_data = "";
    // console.log($("input:radio[name='item_select']:checked").val());
    switch ($("input:radio[name='item_select']:checked").val()) {
      case "current_sale":
        $select_data = { current_sale: "1" };
        break;
      case "all_item":
        $select_data = { all_item: "1" };
        $select_data = { price_low: 1, price_high: 999999 };
        break;
      case "choice":
        let price_low;
        let price_high;
        let select_item_genre;
        let select_item_tag;
        $select_data = { all_item: "1" };
        $select_data = { price_low: 1, price_high: 999999};
        if ($("#item_sel_price").prop("checked")) {
          price_low = $("input:text[name='item_price_low']").val();
          price_high = $("input:text[name='item_price_high']").val();
          $select_data = { price_low: price_low, price_high: price_high };
        }
        if ($("#item_sel_category").prop("checked")) {
          
          select_item_genre = $("#menu1").val();
          select_item_tag = $("#menu2").val();
          $select_data = { item_genre: select_item_genre, tag: select_item_tag ,price_low: 1, price_high: 999999};
        }
        if ($("#item_sel_price").prop("checked") && $("#item_sel_category").prop("checked")) {
          $select_data = { price_low: price_low, price_high: price_high, item_genre: select_item_genre, tag: select_item_tag };
        }
        // console.log($select_data);
        break;
    }


    //以前表示した中身を消去
    $(".item_list_table").empty();
    $(".item_list_table").append("<tr><th>画像</th><th>商品ID</th><th>商品名</th><th>カテゴリ</th><th>タグ</th><th>価格</th><th>セール予定価格</th></tr>");

    //ajax開始
    $.ajax({
      type: "POST",
      url: "./ajax_itemlist.php",
      data: $select_data,//←ここにほしいデータを入れてく。上記参考

    }).done(function (data) {

      // console.log($.parseJSON(data));//あとで消す！

      item_list_data = $.parseJSON(data);
      for (let i = 0; i < item_list_data.length; i++) {
        $(".item_list_table").append('<tr><td><img class="img" src="../images/thumbnails/owners/' + item_list_data[i]["customer_code"] + '/' + item_list_data[i]["thumbnail"] + '" data-action="zoom"></img></td><td>' + item_list_data[i]["item_code"] + '</td><td class="table_item_name">' + item_list_data[i]["item_name"] + '</td><td class="no_wrap">' + $genre_list[item_list_data[i]["item_genre"]] + '</td><td class="no_wrap">' + $tag_list[item_list_data[i]["item_genre"]][item_list_data[i]["tag"]] + '</td><td class="no_wrap">¥' + item_list_data[i]["price"] + '</td><td class="red no_wrap sale_price">¥' + Math.floor(item_list_data[i]["price"] * ((100 - $("#cam_ratio").val()) / 100)) + '</td></tr>');
      }
      $(".result_count").text("対象商品件数" + item_list_data.length + "件");

      // if($("#cam_discount").val() != ""){

      // }
    });
  };

  // $("button").click(function () {
  //   console.log("returnfal");
  //   return false;

  // });
  //編集確認ダイアログ
  $("input:submit[name='hold_btn']").click(function(){
    if(item_list_click_flg){//商品リストを確認したか？
      if (confirm("編集を実行しますか？")) {
        return true;
      }else{
        alert("処理を中止しました。");
        return false;
      }
    }else{
      alert("キャンペーン対象商品を一度確認してください。");
      return false;
    }

  });
  //削除確認ダイアログ
  $("input:submit[name='delete_btn']").click(function(){
    if (confirm("すべての商品のセールを終了します。よろしいですか？")) {
      return true;
    }else{
      alert("処理を中止しました。");
      return false;
    }
  });

  //フォームバリデーション
  $("#cam_form").validate({
    //ルール
    rules: {
      cam_name: {
        required: true, 
      },
      cam_detail: {
        required: true, 
       },
       cam_ratio: {
        required: true,
        min:1,
        max:100,
      },
      cam_left_count: {
        required: true,
        min:1,
        max:10000,
      },      
      images: {
        image_rule: true, 
      },
    },

    // エラーメッセージ
    messages: {
      cam_name: {
        required: 'キャンペーン名を入力してください',
        },
      cam_detail: {
        required: 'キャンペーン内容を入力してください',
      },
      cam_ratio: {
        required: '割引率を入力してください',
        min: '入力最小値は1です',
        max: '入力最大値は100です',
      },
      cam_left_count: {
        required: '割引限定数を入力してください',
        min: '入力最小値は1です',
        max: '入力最大値は10000です',
      },
      images: {
        image_rule: '画像を選択してください',
      },
    },

    // エラーメッセージ出力箇所
    errorPlacement: function(error, element){
        $(".msg").text("");
        // $(".err_msg").text("");
        // $(".is-error-"+name).text("");
        var name = element.attr('name');
        error.appendTo($('.is-error-'+name));
    },
    errorElement: "span",
    errorClass: "is-error",
  });

  //画像用独自ルール
  $.validator.addMethod('image_rule', function(value, element) {
    // お決まりの定型文
    // 検証対象の要素にこのルールが設定されているか
    // if ( this.optional( element ) ) {
    //     return true;
    // }
    //現在の画像を使用のチェックなし＆画像が入ってなければアウト
    if($("#image_check1").prop("checked")== false){
     if(value.length == 0){
        return false;
     }
    }
    return true;
}, '画像を選択してください');



});