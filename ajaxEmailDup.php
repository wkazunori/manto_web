

<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　AjaxEmailDup　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

// postがあり、ユーザーIDがあり、ログインしている場合
if (isset($_POST['emailDup'])) {

  debug('POST送信があります。');
  debug("emailDupの値" . print_r($_POST['emailDup'], true));
  $emailDup = $_POST['emailDup'];

  validEmailDup($emailDup);
  debug("err_msgの値" . print_r($err_msg['email'], true));

  echo json_encode($err_msg);
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>