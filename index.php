<?php
session_start();
require('library.php');

//ログインしていいるかどうかの確認
if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
    $id = $_SESSION['id']; //直接使うのは避けよう（ログインのid）
    $name = $_SESSION['name'];
} else {
    header('Location: login.php');
    exit();
}

$dbh = dbconnect();

//メッセージの投稿
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $stmt = $dbh->prepare('insert into posts (message, member_id) values(?, ?)');
    if (!$stmt) {
        die($dbh->error);
    }

    $stmt->bindParam(1, $message);
    $stmt->bindParam(2, $id);
    $success = $stmt->execute();
    if (!$success) {
        die($dbh->error);
    }

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="style.css"/>
</head>

<body>
<div id="wrap">
    <div id="head">
        <h1>ひとこと掲示板</h1>
    </div>
    <div id="content">
        <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
        <form action="" method="post">
            <dl>
                <dt><?php echo h($name); ?>さん、メッセージをどうぞ</dt>
                <dd>
                    <textarea name="message" cols="50" rows="5"></textarea>
                </dd>
            </dl>
            <div>
                <p>
                    <input type="submit" value="投稿する"/>
                </p>
            </div>
        </form>

        <?php 
        $stmt = $dbh->prepare('select p.id, p.member_id, p.message, p.created, m.name, m.picture from posts p, members m where p.member_id=? and m.id=p.member_id order by id desc '); //ログインidとSQLidを結合している
        if (!$stmt) {
            die($dbh->error);
        }
        $stmt->bindParam(1, $id);
        $success = $stmt->execute();
        if (!$success) {
            die($dbh->error);
        }

        while($result = $stmt->fetch(PDO::FETCH_NUM)): 
            $post_id = $result[0];
            $member_id = $result[1]; //これはログインの時のidではない。投稿した人のid
            $message = $result[2];
            $created = $result[3];
            $picture = $result[5]; 
        ?> 

        <div class="msg">
            <?php if ($picture): ?>
            <img src="member_picture/<?php echo h($picture); ?>" width="48" height="48" alt=""/>
            <?php endif; ?>
            <p><?php echo h($message); ?><span class="name">(<?php echo h($name); ?>)</span></p>
            <p class="day">
                <a href="view.php?id="><?php echo h($created); ?></a>
                <?php if ($_SESSION['id'] === $member_id): ?>
                [<a href="delete.php?id=<?php echo h($post_id); ?>" style="color: #F33;">削除</a>]
                <?php endif; ?>
            </p>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>

</html>

<!-- $id と $member_id の違い
$id:
ログインしている「現在のユーザーのID」を表します（セッションから取得）。

$member_id:
データベースから取得した「投稿をしたユーザーのID」を表します。 -->