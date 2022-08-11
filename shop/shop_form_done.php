<?php
session_start();
session_regenerate_id(true);
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
try {
    require_once '../common/common.php';
    require_once '../dbConfig.php';

    $post = sanitize($_POST);

    $onamae = $post['onamae'];
    $email = $post['email'];
    $zip = $post['zip'];
    $address = $post['address'];
    $tel = $post['tel'];
    $chumon = $post['chumon'];
    $pass = $post['pass'];
    $danjo = $post['danjo'];
    $birth = $post['birth'];

    echo $onamae . '様<br>';
    echo 'ご注文ありがとうございました。<br>';
    echo $email . 'にメールを送りましたのでご確認ください。<br>';
    echo '商品は以下の住所に発送させていただきます。<br>';
    echo $zip . '<br>';
    echo $address . '<br>';
    echo $tel . '<br>';

    $honbun = '';
    $honbun .= "{$onamae}様\n\nこのたびはご注文ありがとうございました。\n";
    $honbun .= "\n";
    $honbun .= "ご注文商品\n";
    $honbun .= "-------------------------------------------\n";

    $cart = $_SESSION['cart'];
    $kazu = $_SESSION['kazu'];
    $max = count($cart);

    $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_SERVER . ';charset=utf8';
    $dbh = new PDO($dsn, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    for ($i = 0; $i < $max; $i++) {
        $sql = 'SELECT name, price FROM mst_product WHERE code=?';
        $stmt = $dbh->prepare($sql);
        $data[0] = $cart[$i]; // ループが回るたびに1,2,3...とならないため0を明記
        $stmt->execute($data);

        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        $name = $rec['name'];
        $price = $rec['price'];
        $kakaku[] = $price;
        $suryo = $kazu[$i];
        $shokei = $price * $suryo;

        $honbun .= "{$name} ";
        $honbun .= "{$price}円 x ";
        $honbun .= "{$kazu[$i]}個 = ";
        $honbun .= "{$shokei}円\n";
    }

    $sql = 'LOCK TABLES dat_sales WRITE, dat_sales_product WRITE, dat_member WRITE';
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    $lastmembercode = 0;
    if ($chumon == 'chumontouroku') {
        $sql = 'INSERT INTO dat_member(password,name,email,zip,address,tel,danjo,born)
          VALUES(?,?,?,?,?,?,?,?)';
        $stmt = $dbh->prepare($sql);
        $data = array();
        $data[] = md5($pass);
        $data[] = $onamae;
        $data[] = $email;
        $data[] = $zip;
        $data[] = $address;
        $data[] = $tel;
        if ($danjo == 'dan') {
            $data[] = 1;
        } else {
            $data[] = 2;
        }
        $data[] = $birth;
        $stmt->execute($data);

        $sql = 'SELECT LAST_INSERT_ID()';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastmembercode = $rec['LAST_INSERT_ID()'];
    }

    $sql = 'INSERT INTO dat_sales(code_member,name,email,zip,address,tel)
          VALUES(?,?,?,?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data = array();
    $data[] = $lastmembercode;
    $data[] = $onamae;
    $data[] = $email;
    $data[] = $zip;
    $data[] = $address;
    $data[] = $tel;
    $stmt->execute($data);

    $sql = 'SELECT LAST_INSERT_ID()';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $lastcode = $rec['LAST_INSERT_ID()'];

    for ($i = 0; $i < $max; $i++) {
        $sql = 'INSERT INTO dat_sales_product(code_sales,code_product,price,quantity)
              VALUES(?,?,?,?)';
        $stmt = $dbh->prepare($sql);
        $data = array();
        $data[] = $lastcode;
        $data[] = $cart[$i];
        $data[] = $kakaku[$i];
        $data[] = $kazu[$i];
        $stmt->execute($data);
    }

    $sql = 'UNLOCK TABLES';
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    $dbh = null;

    if($chumon=='chumontouroku'){
        echo '会員登録が完了しました。<br>';
        echo '次回からメールアドレスとパスワードでログインしてください。<br>';
        echo 'ご注文が簡単にできるようになります。<br>';
        echo '<br>';
    }

    $honbun .= "送料は無料です\n";
    $honbun .= "-------------------------------------------\n";
    $honbun .= "\n";
    $honbun .= "代金は以下の口座にお振込みください。\n";
    $honbun .= "ろくまる銀行 やさい支店 普通口座１２３４５６７\n";
    $honbun .= "入金確認が取れ次第、梱包、発送させていただきます。\n";
    $honbun .= "\n";

    if($chumon=='chumontouroku'){
        echo "会員登録が完了しました。\n";
        echo "次回からメールアドレスとパスワードでログインしてください。\n";
        echo "ご注文が簡単にできるようになります。\n";
        echo "\n";
    }

    $honbun .= "□□□□□□□□□□□□□□□□□□□□\n";
    $honbun .= "～安心野菜のろくまる農園～\n";
    $honbun .= "\n";
    $honbun .= "〇〇県六丸郡六丸村 123-4\n";
    $honbun .= "電話 090-6060-xxxx\n";
    $honbun .= "メール info@rokumarunoen.co.jp\n";
    $honbun .= "□□□□□□□□□□□□□□□□□□□□\n";


//    echo '<br>';
//    echo nl2br($honbun);

    $title = 'ご注文ありがとうございます。';
    $header = 'From: info@rokumarunouen.co.jp';
    $honbun = html_entity_decode($honbun, ENT_QUOTES, 'UTF-8');
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');
    mb_send_mail($email, $title, $honbun, $header);

    $title = 'お客様からご注文がありました。';
    $header = 'From: ' . $email;
    $honbun = html_entity_decode($honbun, ENT_QUOTES, 'UTF-8');
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');
    mb_send_mail('spherenov@gmail.com', $title, $honbun, $header);

    // カートを空にする
    unset($_SESSION['cart']);
    unset($_SESSION['kazu']);

} catch (Exception $e) {
    echo $e->getMessage();
    echo 'ただいま障害により大変ご迷惑をおかけしております。';
    exit();
}
?>

<br>
<a href="./shop_list.php">商品画面へ</a>

</body>
</html>
