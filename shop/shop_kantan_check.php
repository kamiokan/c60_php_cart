<?php
session_start();
session_regenerate_id(true);
if (isset($_SESSION['member_login']) === false) {
    echo 'ログインされていません<br>';
    echo '<a href="./shop_list.php">商品一覧へ</a>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="./css/style.css" rel="stylesheet" type="text/css">
    <title>ろくまる農園</title>
</head>
<body>

<?php
include_once("../dbConfig.php");

$code=$_SESSION['member_code'];

$dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_SERVER . ';charset=utf8';
$dbh = new PDO($dsn, DB_USER, DB_PASS);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = 'SELECT name,email,zip,address,tel FROM dat_member WHERE code=?';
$stmt = $dbh->prepare($sql);
$data[] = $code;
$stmt->execute($data);
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$dbh=null;

$onamae=$rec['name'];
$email=$rec['email'];
$zip=$rec['zip'];
$address=$rec['address'];
$tel=$rec['tel'];

echo 'お名前<br>';
echo $onamae;
echo '<br><br>';

echo 'メールアドレス<br>';
echo $email;
echo '<br><br>';

echo '郵便番号<br>';
echo $zip;
echo '<br><br>';

echo '住所<br>';
echo $address;
echo '<br><br>';

echo '電話番号<br>';
echo $tel;
echo '<br><br>';

echo '<form method="post" action="shop_kantan_done.php">';
echo '<input type="hidden" name="onamae" value="' . $onamae . '">';
echo '<input type="hidden" name="email" value="' . $email . '">';
echo '<input type="hidden" name="zip" value="' . $zip . '">';
echo '<input type="hidden" name="address" value="' . $address . '">';
echo '<input type="hidden" name="tel" value="' . $tel . '">';
echo '<input type="button" onclick="history.back();" value="戻る">';
echo '<input type="submit" value="OK"><br>';
echo '</form>';

?>

</body>
</html>
