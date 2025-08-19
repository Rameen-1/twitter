<?php
session_start();
include 'config.php';
 
if (!isset($_SESSION['user_id']) || !isset($_POST['followed_id'])) {
    header("Location: index.php");
    exit();
}
 
$follower_id = $_SESSION['user_id'];
$followed_id = intval($_POST['followed_id']);
 
if ($follower_id === $followed_id) {
    exit("You can't follow yourself.");
}
 
// Check if already following
$check = $conn->prepare("SELECT id FROM followers WHERE follower_id=? AND followed_id=?");
$check->bind_param("ii", $follower_id, $followed_id);
$check->execute();
$check->store_result();
 
if ($check->num_rows > 0) {
    // Unfollow
    $delete = $conn->prepare("DELETE FROM followers WHERE follower_id=? AND followed_id=?");
    $delete->bind_param("ii", $follower_id, $followed_id);
    $delete->execute();
} else {
    // Follow
    $insert = $conn->prepare("INSERT INTO followers (follower_id, followed_id) VALUES (?, ?)");
    $insert->bind_param("ii", $follower_id, $followed_id);
    $insert->execute();
}
 
// Redirect back to the user profile
header("Location: user.php?id=" . $followed_id);
exit();
