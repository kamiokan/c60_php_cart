<?php
session_start();
session_regenerate_id(true);
if (isset($_SESSION['member_login']) === false) {
    echo 'ようこそゲスト様　';
    echo '<a href="./member_login.html">会員ログイン</a><br>';
    echo '<br>';
} else {
    echo 'ようこそ';
    echo $_SESSION['member_name'];
    echo '様　';
    echo '<a href="./member_logout.php">ログアウト</a>';
    echo '<br>';
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

try {
    $pro_code = htmlspecialchars($_GET['procode'], ENT_QUOTES);

    $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_SERVER . ';charset=utf8';
    $dbh = new PDO($dsn, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT name, price, gazou FROM mst_product WHERE code=?';
    $stmt = $dbh->prepare($sql);
    $data[] = $pro_code;
    $stmt->execute($data);

    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $pro_name = $rec['name'];
    $pro_price = $rec['price'];
    $pro_gazou_name = $rec['gazou'];

    $dbh = null;

    if ($pro_gazou_name === '') {
        $disp_gazou = '';
    } else {
        $disp_gazou = '<img src="../product/gazou/' . $pro_gazou_name . '">';
    }

    echo '<a href="shop_cartin.php?procode=' . $pro_code . '">カートに入れる</a><br><br>';
} catch (Exception $e) {
    echo 'ただいま障害により大変ご迷惑をお掛けしております。';
    exit();
}
?>

商品情報参照<br>
<br>
商品コード<br>
<?php echo $pro_code; ?>
<br>
商品名<br>
<?php echo $pro_name; ?><br>
<br>
価格<br>
<?php echo $pro_price; ?>円<br>
<br>
<?php echo $disp_gazou; ?>
<br>
<form method="post" action="pro_edit_check.php">
    <input type="button" onclick="history.back();" value="戻る">
</form>
</body>
</html>
