<?php
session_start();
require('library.php');
$error = [];

// メールアドレスとパスワードをご記入ください
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($email === '' || $password === '') {
        $error['login'] = 'blank';
    } else {
        // ログインに失敗しました。正しくご記入ください。
        $dbh = dbconnect();
        $stmt = $dbh->prepare('select id, name, password from members where email=? limit 1');
        if (!$stmt) {
            die($dbh->error);
        }

        $stmt->bindParam(1, $email);
        $success = $stmt->execute();
        if (!$success) {
            die($dbh->error);
        }

        $result = $stmt->fetch(PDO::FETCH_NUM); //resultに入れる処理
        list($id, $name, $hash) = $result; //FETCH_NUMがないと配列で表示できない

        if (password_verify($password, $hash)) {
            //ログイン成功
            session_regenerate_id();
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            header('Location: index.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <title>ログインする</title>
</head>

<body>
<div id="wrap">
    <div id="head">
        <h1>ログインする</h1>
    </div>
    <div id="content">
        <div id="lead">
            <p>メールアドレスとパスワードを記入してログインしてください。</p>
            <p>入会手続きがまだの方はこちらからどうぞ。</p>
            <p>&raquo;<a href="join/">入会手続きをする</a></p>
        </div>
        <form action="" method="post">
            <dl>
                <dt>メールアドレス</dt>
                <dd>
                    <input type="text" name="email" size="35" maxlength="255" value="<?php echo isset($email) ? h($email) : ''; ?>"/> 
                    <?php if (isset($error['login']) && $error['login'] === 'blank'): ?>
                    <p class="error">* メールアドレスとパスワードをご記入ください</p>
                    <?php endif; ?>
                    <?php if (isset($error['login']) && $error['login'] === 'failed'): ?>
                    <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
                    <?php endif; ?>
                </dd>
                <dt>パスワード</dt>
                <dd>
                    <input type="password" name="password" size="35" maxlength="255" value="<?php echo isset($password) ? h($password) : ''; ?>"/>
                </dd>
            </dl>
            <div>
                <input type="submit" value="ログインする"/>
            </div>
        </form>
    </div>
</div>
</body>
</html>
