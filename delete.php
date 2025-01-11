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

$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$post_id) {
  header('Location: index.php');
}

$dbh = dbconnect();
$stmt = $dbh->prepare('delete from posts where id=? and member_id=? limit 1');
if (!$stmt) {
  die($dbh->error);
}
$stmt->bindParam(1, $post_id);
$stmt->bindParam(2, $id);
$success = $stmt->execute();
if (!$success) {
  die($dbh->error);
}

header('Location: index.php'); exit();
?>
