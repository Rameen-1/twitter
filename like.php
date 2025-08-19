<?php
include 'config.php';
$tweet_id = $_POST['tweet_id'];
$user_id = $_SESSION['user_id'];
 
$stmt = $conn->prepare("INSERT IGNORE INTO likes (user_id, tweet_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $tweet_id);
$stmt->execute();
header("Location: index.php");
 
