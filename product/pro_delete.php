<?php
session_start();
session_regenerate_id(true);
if (isset($_SESSION['login']) === false) {
    echo 'ログインされていません。<br>';
    echo '<a href="../staff_login/staff_login.html">ログイン画面へ</a>';
    exit();
} else {
    echo $_SESSION['staff_name'];
    echo 'さんログイン中<br>';
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
    $sql = 'SELECT name, gazou FROM mst_product WHERE code=?';
    $stmt = $dbh->prepare($sql);
    $data[] = $pro_code;
    $stmt->execute($data);

    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $pro_name = $rec['name'];
    $pro_gazou_name = $rec['gazou'];

    $dbh = null;

    if ($pro_gazou_name == '') {
        $disp_gazou = '';
    } else {
        $disp_gazou = '<img src="./gazou/' . $pro_gazou_name . '">';
    }
} catch (Exception $e) {
    echo 'ただいま障害により大変ご迷惑をお掛けしております。';
    exit();
}
?>

商品削除<br>
<br>
商品コード<br>
<?php echo $pro_code; ?>
<br>
商品名<br>
<?php echo $pro_name; ?>
<br>
<?php echo $disp_gazou; ?>
<br>
この商品を削除してよろしいですか？
<br>
<?php // todo CSRF対策する ?>
<form method="post" action="pro_delete_done.php">
    <input type="hidden" name="code" value="<?php echo $pro_code; ?>">
    <input type="hidden" name="gazou_name" value="<?php echo $pro_gazou_name; ?>">
    <input type="button" onclick="history.back();" value="戻る">
    <input type="submit" value="OK">
</form>
</body>
</html>
