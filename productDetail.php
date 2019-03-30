<?php
 //共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　商品詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// 商品IDのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';

// DBから商品データを取得
$viewData = getProductOne($p_id);
// パラメータに不正な値が入っているかチェック
if (empty($viewData)) {
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
debug('取得したDBデータ：' . print_r($viewData, true));

//--閲覧履歴機能を作成--

if (!empty($_SESSION['login_date'])) {
  //login済みユーザー
  debug('閲覧履歴作成:ログイン済みユーザー');

  //DBのwatchテーブルに閲覧情報を格納していく
  try {
    // DBへ接続
    $dbh = dbConnect();

    // SQL文作成
    $sql = 'INSERT INTO watch (user_id, product_id, create_date) VALUES (:u_id, :p_id, :date)';
    $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
} else {
  //loginしていない場合
  debug('閲覧履歴作成:ログインしていないユーザー');

  //閲覧履歴用に$p_idを配列に格納(MAX3)
  if (!empty($_SESSION['hist_log'])) {
    $history = $_SESSION['hist_log'];
    debug('hist_logがある場合の値:' . print_r($history, true));

    //$p_idがすでにあれば古いのを消す
    $target = array_search($p_id, $history, true);

    // if (isset($target)) {
    // if (!empty($target)) {
    if ($target !== false) { //array_searchで出力された添字が0の場合、ifの処理をスルーする場合があるので
      debug('$targetがある場合の値:' . $target);
      unset($history[$target]);
      //indexを詰める
      $history = array_values($history);
    }

    //配列の要素数が3つあれば配列の先頭を消す
    if (count($history) == 3) {
      array_shift($history);
    }

    //p_idを配列の最後尾に入れる
    $history[] = $p_id;
  } else {
    debug('hist_logが無いので初期値をセット');
    $history = array();
    $history[] = $p_id;
  }
  debug('$historyの出来上がり:' . print_r($history, true));
  $_SESSION['hist_log'] = $history;
}
//--閲覧履歴機能を作成end--

// post送信されていた場合
if (!empty($_POST['submit'])) {
  debug('POST送信があります。');

  //ログイン認証
  require 'auth.php';

  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();

    //購入ユーザーと販売ユーザー用の掲示板を作成
    // SQL文作成
    $sql = 'INSERT INTO bord (sale_user, buy_user, product_id, create_date) VALUES (:s_uid, :b_uid, :p_id, :date)';
    $data = array(':s_uid' => $viewData['user_id'], ':b_uid' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if ($stmt) {
      $_SESSION['msg_success'] = SUC05;
      debug('連絡掲示板へ遷移します。');
      header("Location:msg.php?m_id=" . $dbh->lastInsertID()); //連絡掲示板へ
    }

    //productテーブルに購入flgと購入ユーザーの情報を挿入
    // SQL文作成
    $sql = "UPDATE product SET buy_user = :b_uid, buy_flg = 1 WHERE id = :p_id";
    $data = array(':b_uid' => $_SESSION['user_id'], ':p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = '商品詳細';
require('head.php');
?>

<body class="page-productDetail page-1colum">
    <style>
        .badge {
            padding: 5px 10px;
            color: white;
            background: #7acee6;
            margin-right: 10px;
            font-size: 16px;
            vertical-align: middle;
            position: relative;
            top: -4px;
        }

        .badge-shipment {
            margin-left: 5px;
            margin-right: 10px;
            border: solid 1px #ccc;
            border-radius: 10px;
            padding: 5px 10px;
            background: #FFF;
            color: #666666;
            font-size: 16px;
            vertical-align: middle;
            position: relative;
            top: -4px;
        }

        #main .title {
            font-size: 28px;
            padding: 10px 0;
        }

        .product-img-container {
            overflow: hidden;
        }

        .product-img-container img {
            width: 100%;
        }

        .product-img-container .img-main {
            width: 750px;
            float: left;
            padding-right: 15px;
            box-sizing: border-box;
        }

        .product-img-container .img-sub {
            width: 230px;
            float: left;
            background: #f6f5f4;
            padding: 15px;
            box-sizing: border-box;
        }

        .product-img-container .img-sub:hover {
            cursor: pointer;
        }

        .product-img-container .img-sub img {
            margin-bottom: 15px;
        }

        .product-img-container .img-sub img:last-child {
            margin-bottom: 0;
        }

        .product-detail {
            background: #f6f5f4;
            padding: 15px;
            margin-top: 15px;
            min-height: 150px;
        }

        .product-buy {
            overflow: hidden;
            margin-top: 15px;
            margin-bottom: 50px;
            height: 50px;
            line-height: 50px;
        }

        .product-buy .item-left {
            float: left;
        }

        .product-buy .item-right {
            float: right;
        }

        .product-buy .price {
            font-size: 32px;
            margin-right: 30px;
        }

        .product-buy .btn {
            border: none;
            font-size: 18px;
            padding: 10px 30px;
        }

        .product-buy .btn:hover {
            cursor: pointer;
        }

        .product-buy .btn.btn-sold:hover {
            cursor: not-allowed;
        }

        /*お気に入りアイコン*/
        .icn-like {
            float: right;
            color: #ddd;
        }

        .icn-like:hover {
            cursor: pointer;
        }

        .icn-like.active {
            float: right;
            color: #fe8a8b;
        }
    </style>

    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main">

            <div class="title">
                <span class="badge"><?php echo sanitize($viewData['category']); ?></span>
                <?php echo sanitize($viewData['name']); ?>
                <span class="badge-shipment"><?php echo sanitize($viewData['shipment']); ?></span>
                <i class="fa fa-heart icn-like js-click-like <?php if (isLike($_SESSION['user_id'], $viewData['id'])) {
                                                                echo 'active';
                                                              } ?>" aria-hidden="true" data-productid="<?php echo sanitize($viewData['id']); ?>"></i>
            </div>
            <div class="product-img-container">
                <div class="img-main">
                    <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="メイン画像：<?php echo sanitize($viewData['name']); ?>" id="js-switch-img-main">
                </div>
                <div class="img-sub">
                    <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="画像1：<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
                    <img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="画像2：<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
                    <img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>" alt="画像3：<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
                </div>
            </div>
            <div class="product-detail">
                <p><?php echo sanitize($viewData['comment']); ?></p>
            </div>
            <div class="product-buy">
                <div class="item-left">
                    <a href="index.php<?php echo appendGetParam(array('p_id')); ?>">&lt; 商品一覧に戻る</a>
                </div>
                <form action="" method="post">
                    <!-- formタグを追加し、ボタンをinputに変更し、style追加 -->
                    <?php 
                    if (sanitize($viewData['buy_flg']) == 1) {
                      ?>
                    <div class="item-right btn-sold-wrapper">
                        <input type="submit" value="売り切れました" name="submit" class="btn btn-sold" style="margin-top:0;" disabled>
                    </div>
                    <?php 
                  } else {
                    ?>
                    <div class="item-right">
                        <input type="submit" value="買う!" name="submit" class="btn btn-primary" style="margin-top:0;">
                    </div>
                    <?php 
                  } ?>
                </form>
                <div class="item-right">
                    <p class="price">¥<?php echo sanitize(number_format($viewData['price'])); ?>-</p>
                </div>
            </div>

        </section>

    </div>

    <!-- footer -->
    <?php
    require('footer.php');
    ?> 