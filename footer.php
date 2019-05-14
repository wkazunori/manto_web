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
      validSign();
    });
    $('#js-sign-pass').on("blur", function() {
      validSign();
    });
    $('#js-sign-passRe').on("blur", function() {
      validSign();
    });

    function validEmailSign() {

      var emailRegExp = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;
      var passRegExp = /^([a-zA-Z0-9]{6,})$/;

      //emailのバリデーション
      var email = $("#js-sign-email").val();
      //①email形式かチェック
      if (email) {
        var emailValidRst = emailRegExp.test(email);
        if (!emailValidRst) {
          $("#js-email-msg").text(validReturn.emailMsg);
        } else {
          $("#js-email-msg").text("");

          $.when()
          //②emailが重複してるかチェック
          $.ajax({
            type: "post",
            url: "ajaxEmailDup.php",
            dataType: "json",
            data: {
              'emailDup': email
            }
          }).then(function(data) {
            if (data) {
              console.log(data);
              $("#js-email-msg").text(data.email);
            }
          });
        }
      }

      //passのバリデーション
      var pass = $("#js-sign-pass").val();
      if (pass) {
        var passValidRst = passRegExp.test(pass);
        if (!passValidRst) {
          $("#js-pass-msg").text(validReturn.passMsg);
        } else {
          $("#js-pass-msg").text("");
        }
      }

      //passReのバリデーション
      var passRe = $("#js-sign-passRe").val();
      var passReValidRst = "";
      if (passRe) {
        if (pass === passRe) {
          passReValidRst = true;
          $("#js-passRe-msg").text("");
        } else {
          passReValidRst = false;
          $("#js-passRe-msg").text(validReturn.passReMsg);
        }
      }

      // ①形式のチェックを通過してたらtrue
      if (emailValidRst && passValidRst && passReValidRst) {
        var validRst = true;
      }

      // ②さらに重複チェックでエラーメッセージが無かったらtrue
      var emailValidMsg = $("#js-email-msg").val();
      if (!emailValidMsg) {
        var validMsg = true;
      }

      //③両方trueで登録ボタンのdisabledを解除
      if (validRst && validMsg) {
        $('#js-sign-button').prop("disabled", false); //表示
      } else {
        $('#js-sign-button').prop("disabled", true); //非表示
      }
    }

    //ローディングを表示
    // function displayLoad() {
    //   var loadIconSrc = "img/gif-load.gif";
    //   var loadIconView = "<img id=\"js-load-icon\" src=\"${loadIconSrc}\">";

    //   $("#js-sign-email").after(loadIconView);
    // }
    //ローディングを非表示
    // function removeDisplayLoad() {
    //   $("#js-load-icon").remove();
    // }

  });
</script>
</body>

</html>