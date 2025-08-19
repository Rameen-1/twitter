<?php
session_start();
include 'config.php';
 
if (!isset($_SESSION['user_id']) || !isset($_POST['target_user_id'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit();
}
 
$current_user = $_SESSION['user_id'];
$target_user = (int) $_POST['target_user_id'];
 
// Check if already following
$check = $conn->prepare("SELECT * FROM follows WHERE follower_id=? AND following_id=?");
$check->bind_param("ii", $current_user, $target_user);
$check->execute();
$result = $check->get_result();
 
if ($result->num_rows > 0) {
    // Already following — unfollow
    $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id=? AND following_id=?");
    $stmt->bind_param("ii", $current_user, $target_user);
    $stmt->execute();
} else {
    // Not following — follow
    $stmt = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $current_user, $target_user);
    $stmt->execute();
}
 
// ✅ Redirect back to the profile
header("Location: profile.php?id=" . $target_user);
exit();
?>
