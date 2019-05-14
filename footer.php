<footer id="footer">
  Copyright <a href="http://webukatu.com/">ウェブカツ!!WEBサービス部</a>. All Rights Reserved.
</footer>

<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script>
  $(function() {

    // フッターを最下部に固定
    var $ftr = $('#footer');
    if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
      $ftr.attr({
        'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'
      });
    }
    // メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if (msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
      $jsShowMsg.slideToggle('slow');
      setTimeout(function() {
        $jsShowMsg.slideToggle('slow');
      }, 5000);
    }

    // 画像ライブプレビュー
    var $dropArea = $('.area-drop');
    var $fileInput = $('.input-file');
    $dropArea.on('dragover', function(e) {
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', '3px #ccc dashed');
    });
    $dropArea.on('dragleave', function(e) {
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', 'none');
    });
    $fileInput.on('change', function(e) {
      $dropArea.css('border', 'none');
      var file = this.files[0], // 2. files配列にファイルが入っています
        $img = $(this).siblings('.prev-img'), // 3. jQueryのsiblingsメソッドで兄弟のimgを取得
        fileReader = new FileReader(); // 4. ファイルを読み込むFileReaderオブジェクト

      // 5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
      fileReader.onload = function(event) {
        // 読み込んだデータをimgに設定
        $img.attr('src', event.target.result).show();
      };

      // 6. 画像読み込み
      fileReader.readAsDataURL(file);

    });

    // テキストエリアカウント
    var $countUp = $('#js-count'),
      $countView = $('#js-count-view');
    $countUp.on('keyup', function(e) {
      $countView.html($(this).val().length);
    });

    // 画像切替
    var $switchImgSubs = $('.js-switch-img-sub'),
      $switchImgMain = $('#js-switch-img-main');
    $switchImgSubs.on('click', function(e) {
      $switchImgMain.attr('src', $(this).attr('src'));
    });

    // お気に入り登録・削除
    var $like,
      likeProductId;
    $like = $('.js-click-like') || null; //nullというのはnull値という値で、「変数の中身は空ですよ」と明示するためにつかう値
    likeProductId = $like.data('productid') || null;
    // 数値の0はfalseと判定されてしまう。product_idが0の場合もありえるので、0もtrueとする場合にはundefinedとnullを判定する
    if (likeProductId !== undefined && likeProductId !== null) {
      $like.on('click', function() {
        var $this = $(this);
        $.ajax({
          type: "POST",
          url: "ajaxLike.php",
          data: {
            productId: likeProductId
          }
        }).done(function(data) {
          console.log('Ajax Success');
          // クラス属性をtoggleでつけ外しする
          $this.toggleClass('active');
        }).fail(function(msg) {
          console.log('Ajax Error');
        });
      });
    }

    //================
    //ユーザー登録のバリデーションチェック
    //================

    var validReturn = {
      'emailMsg': 'Emailの形式で入力してください',
      'passMsg': '半角英数字かつ6文字以上でご利用いただけます',
      'passReMsg': 'パスワード（再入力）が合っていません',
    }

    $('#js-sign-email').on("blur", function() {
      var emailRst = validEmailSign();
      var passRst = validPassSign();
      var passReRst = validPassReSign();
      if (emailRst) {
        callAjaxEmailDup(emailRst, passRst, passReRst);
      }
    });

    $('#js-sign-pass').on("blur", function() {
      var emailRst = validEmailSign();
      var passRst = validPassSign();
      var passReRst = validPassReSign();
      if (emailRst) {
        callAjaxEmailDup(emailRst, passRst, passReRst);
      }
    });

    $('#js-sign-passRe').on("blur", function() {
      var emailRst = validEmailSign();
      var passRst = validPassSign();
      var passReRst = validPassReSign();
      if (emailRst) {
        callAjaxEmailDup(emailRst, passRst, passReRst);
      }
    });

    function validEmailSign() {
      //emailのバリデーション
      var emailRegExp = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;

      var email = $("#js-sign-email").val();
      //email形式かチェック
      if (email) {
        var emailValidRst = emailRegExp.test(email);
        if (!emailValidRst) {
          $("#js-email-msg").text(validReturn.emailMsg);
          var rst = "";
          return rst;
        } else {
          $("#js-email-msg").text("");
          var rst = true;
          return rst;
        }
      }
    }

    function ajaxEmailDup() {
      var email = $("#js-sign-email").val();
      return $.ajax({
        type: "post",
        url: "ajaxEmailDup.php",
        dataType: "json",
        data: {
          'emailDup': email
        },
        //リクエストが完了するまで実行される
        beforeSend: function() {
          displayLoad();
        }
      })
    }

    function callAjaxEmailDup(emailRst, passRst, passReRst) {
      ajaxEmailDup().done(function(data) {
        if (data) { //重複した場合エラーメッセージが返ってくる
          $("#js-email-msg").text(data.email);
          emailRst = "";
        } else { //エラーメッセージが無い → dataが無い場合は重複なし判定
          $("#js-email-msg").text("");
        }
      }).always(function(data) { //ajax終了後に登録ボタンの許可するかを判定
        removeDisplayLoad();
        validAllSign(emailRst, passRst, passReRst);
      });
    }

    function validPassSign() {
      //passのバリデーション
      var passRegExp = /^([a-zA-Z0-9]{6,})$/;
      var pass = $("#js-sign-pass").val();
      if (pass) {
        var passValidRst = passRegExp.test(pass);
        if (!passValidRst) {
          $("#js-pass-msg").text(validReturn.passMsg);
          var rst = "";
          return rst;
        } else {
          $("#js-pass-msg").text("");
          var rst = true;
          return rst;
        }
      }
    }

    function validPassReSign() {
      //passReのバリデーション
      var pass = $("#js-sign-pass").val();
      var passRe = $("#js-sign-passRe").val();

      if (passRe) {
        if (pass === passRe) {
          $("#js-passRe-msg").text("");
          var rst = true;
          return rst;
        } else {
          $("#js-passRe-msg").text(validReturn.passReMsg);
          var rst = "";
          return rst;
        }
      }
    }

    function validAllSign(a, b, c) {

      console.log("a:" + a);
      console.log("b:" + b);
      console.log("c:" + c);
      //全引数がtrueで登録ボタンのdisabledを解除
      if (a && b && c) {
        $('#js-sign-button').prop("disabled", false); //表示
      } else {
        $('#js-sign-button').prop("disabled", true); //非表示
      }
    }
    //ローディングを表示
    function displayLoad() {
      // var loadIconSrc = "img/gif-load.gif"; //変数展開失敗 原因聞きたい
      var loadIconView = "<img id=\"js-load-icon\" src=\"img/gif-load.gif\">";
      $("#js-sign-email").after(loadIconView);
    }

    // ローディングを非表示
    function removeDisplayLoad() {
      $("#js-load-icon").remove();
    }

    //================
    //マイページのメニュー ドロップダウン化
    //================
    $(".js-menu-main").hover(
      function() {
        $(".js-menu-sub:not(:animated)", this).slideDown();
      },
      function() {
        $(".js-menu-sub:not(:animated)", this).slideUp();
      });

  });
</script>
</body>

</html>