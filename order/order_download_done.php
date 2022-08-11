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

$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];

try {

    $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_SERVER . ';charset=utf8';
    $dbh = new PDO($dsn, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = '
    SELECT
        dat_sales.code,
        dat_sales.date,
        dat_sales.code_member,
        dat_sales.name AS dat_sales_name,
        dat_sales.email,
        dat_sales.zip,
        dat_sales.address,
        dat_sales.tel,
        dat_sales_product.code_product,
        mst_product.name AS mst_product_name,
        dat_sales_product.price,
        dat_sales_product.quantity
    FROM
        dat_sales, dat_sales_product, mst_product
    WHERE
        dat_sales.code=dat_sales_product.code_sales
        AND dat_sales_product.code_product=mst_product.code
        AND substr(dat_sales.date,1,4)=?
        AND substr(dat_sales.date,6,2)=?
        AND substr(dat_sales.date,9,2)=?
    ';
    $stmt = $dbh->prepare($sql);
    $data[] = $year;
    $data[] = $month;
    $data[] = $day;
    $stmt->execute($data);

    $dbh = null;

    $csv = '注文コード,注文日時,会員番号,お名前,メール,郵便番号,住所,TEL,商品コード,価格,数量';
    $csv .= "\n";
    while (true) {
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rec == false) {
            break;
        }
        $csv .= $rec['code'];
        $csv .= ',';
        $csv .= $rec['date'];
        $csv .= ',';
        $csv .= $rec['code_member'];
        $csv .= ',';
        $csv .= $rec['dat_sales_name'];
        $csv .= ',';
        $csv .= $rec['email'];
        $csv .= ',';
        $csv .= $rec['zip'];
        $csv .= ',';
        $csv .= $rec['address'];
        $csv .= ',';
        $csv .= $rec['tel'];
        $csv .= ',';
        $csv .= $rec['code_product'];
        $csv .= ',';
        $csv .= $rec['mst_product_name'];
        $csv .= ',';
        $csv .= $rec['price'];
        $csv .= ',';
        $csv .= $rec['quantity'];
        $csv .= "\n";
    }

//    echo nl2br($csv);

    $file = fopen('./chumon.csv', 'w');
    $csv = mb_convert_encoding($csv, 'SJIS', 'UTF-8');
    fputs($file, $csv);
    fclose($file);

} catch (Exception $e) {
    echo 'ただいま障害により大変ご迷惑をお掛けしております。';
    exit();
}
?>

<a href="./chumon.csv">注文データのダウンロード</a><br>
<br>
<a href="order_download.php">日付選択へ</a><br>
<br>
<a href="../staff_login/staff_top.php">トップメニューへ</a><br>

</body>
</html>
