<?php
 //共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　販売履歴　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth.php');

// 画面表示用データ取得
//================================
$u_id = $_SESSION['user_id'];
// DBから商品データを取得
$productData = getMySaleProducts($u_id);

// DBからきちんとデータがすべて取れているかのチェックは行わず、取れなければ何も表示しないこととする
debug('取得した商品データ：' . print_r($productData, true));
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '販売履歴';
require('head.php');
?>

<body class="page-withdraw page-1colum">

    <style>
        .form .btn {
            float: none;
        }

        .form {
            text-align: center;
        }
    </style>

    <!-- メニュー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
        <!-- Main -->
        <section id="main">
            <h1 class="page-title">販売履歴</h1>

            <section class="list panel-list">
                <h2 class="title" style="margin-bottom:15px;">
                    売却済み商品一覧
                </h2>
                <?php
                if (!empty($productData)) :
                  foreach ($productData as $key => $val) :
                    ?>
                <a href="productDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&p_id=' . $val['id'] : '?p_id=' . $val['id']; ?>" class="panel pane-">
                    <div class="panel-head">
                        <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="<?php echo sanitize($val['name']); ?>">
                    </div>
                    <div class="panel-body">
                        <p class="panel-title"><?php echo sanitize($val['name']); ?> <span class="price">¥<?php echo sanitize(number_format($val['price'])); ?></span></p>
                    </div>
                </a>
                <?php
              endforeach;
            endif;
            ?>
            </section>
            <a href="mypage.php">&lt; マイページに戻る</a>
        </section>
    </div>

    <!-- footer -->
    <?php
    require('footer.php');
    ?> 