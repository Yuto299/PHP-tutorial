<?php
session_start();
require('library.php');

// ログインしているかどうかの確認
if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
    $id = $_SESSION['id']; 
    $name = $_SESSION['name'];
} else {
    header('Location: login.php');
    exit();
}

// URL から投稿 ID を取得
$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$post_id) {
    header('Location: index.php');
    exit();
}

$dbh = dbconnect();

// 特定の投稿を取得
$stmt = $dbh->prepare('SELECT p.id, p.member_id, p.message, p.created, m.name, m.picture 
                    FROM posts p, members m 
                    WHERE p.id = ? AND m.id = p.member_id');
$stmt->bindParam(1, $post_id, PDO::PARAM_INT);
$stmt->execute();

$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die('その投稿は削除されたか、URLが間違えています');
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
        <h1>投稿詳細</h1>
    </div>
    <div id="content">
        <p>&laquo;<a href="index.php">一覧にもどる</a></p>

        <div class="msg">
            <?php if ($post['picture']): ?>
                <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt=""/>
            <?php endif; ?>
            <p><?php echo h($post['message']); ?><span class="name">(<?php echo h($post['name']); ?>)</span></p>
            <p class="day"><?php echo h($post['created']); ?></p>
        </div>
    </div>
</div>
</body>

</html>