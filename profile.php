<?php
session_start();
include 'config.php';
 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
 
$current_user_id = $_SESSION['user_id'];
 
// Determine if viewing own profile or another user
if (isset($_GET['id']) && $_GET['id'] != $current_user_id) {
    $user_id = (int) $_GET['id'];
} else {
    $user_id = $current_user_id;
}
 
$stmt = $conn->prepare("SELECT username, email, profile FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $profile);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <a href="logout.php" class="logout-btn">Logout</a>
    <h2><?= ($user_id === $current_user_id) ? 'Your Profile' : "$username's Profile" ?></h2>
    <img src="<?= $profile ?>" class="profile-pic" alt="Profile Picture">
    <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
 
    <?php
    // Only show follow/unfollow if viewing someone else's profile
    if ($user_id !== $current_user_id) {
        $check_follow = $conn->prepare("SELECT * FROM follows WHERE follower_id = ? AND following_id = ?");
        $check_follow->bind_param("ii", $current_user_id, $user_id);
        $check_follow->execute();
        $follow_result = $check_follow->get_result();
        $isFollowing = $follow_result->num_rows > 0;
        $check_follow->close();
        ?>
        <form method="POST" action="follow_action.php">
            <input type="hidden" name="target_user_id" value="<?= $user_id ?>">
            <button type="submit"><?= $isFollowing ? 'Unfollow' : 'Follow' ?></button>
        </form>
    <?php } ?>
</div>
</body>
</html>
 
 
 
