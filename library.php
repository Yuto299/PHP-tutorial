<?php
//htmlspecialcharsを短くする
function h($value) {
    return htmlspecialchars($value, ENT_QUOTES); //ボタンを押しても表示できる
} 

//dbへの接続
function dbconnect() {
    $dsn = 'mysql:dbname=min_bbs;host=127.0.0.1;port=3306;charset=utf8';
    $user = 'root';
    $password = '';

    try {
        $dbh = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
    ]);
    } catch (PDOException $e) {
        echo '接続に失敗しました: ' . $e->getMessage();
    }

    return $dbh;
}
?>