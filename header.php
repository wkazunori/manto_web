<header>
  <div class="site-width">
    <h1><a href="index.php">WEBUKATU MARKET</a></h1>
    <nav id="top-nav">
      <ul>
        <?php
        if (empty($_SESSION['user_id'])) {
          ?>
          <li><a href="signup.php" class="btn btn-primary">ユーザー登録</a></li>
          <li><a href="login.php">ログイン</a></li>
        <?php
      } else {
        ?>
          <li class="js-menu-main">
            メニュー
            <ul class="js-menu-sub">
              <li><a href="mypage.php">マイページ</a></li>
              <li><a href="logout.php">ログアウト</a></li>
              <li><a href="registProduct.php">商品を出品する</a></li>
              <li><a href="tranSale.php">販売履歴を見る</a></li>
              <li><a href="profEdit.php">プロフィール編集</a></li>
              <li><a href="passEdit.php">パスワード変更</a></li>
              <li><a href="withdraw.php">退会</a></li>
          </li>
        <?php
      }
      ?>
      </ul>
    </nav>
  </div>
</header>