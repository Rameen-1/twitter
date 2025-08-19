<?php
session_start();
include 'config.php';
 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
 
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
 
// Post a new tweet
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tweet'])) {
    $tweet = trim($_POST['tweet']);
    if (!empty($tweet) && strlen($tweet) <= 160) {
        $stmt = $conn->prepare("INSERT INTO tweets (user_id, content) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $tweet);
        $stmt->execute();
    }
}
 
// Post a new comment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment']) && isset($_POST['tweet_id'])) {
    $comment = trim($_POST['comment']);
    $tweet_id = intval($_POST['tweet_id']);
    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (tweet_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $tweet_id, $user_id, $comment);
        $stmt->execute();
    }
}
 
// Fetch tweets
$tweets = $conn->query("SELECT tweets.id, tweets.content, tweets.created_at, users.username 
                        FROM tweets 
                        JOIN users ON tweets.user_id = users.id 
                        ORDER BY tweets.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Twitter</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
 
    <form method="post" action="">
        <textarea name="tweet" placeholder="What's happening?" maxlength="160" required></textarea>
        <button type="submit">Tweet</button>
    </form>
 
    <h3>Latest Tweets</h3>
 
    <?php while ($row = $tweets->fetch_assoc()): ?>
        <div class="tweet">
            <strong>@<?= htmlspecialchars($row['username']) ?></strong><br>
            <?= htmlspecialchars($row['content']) ?><br>
            <small><?= $row['created_at'] ?></small><br>
 
            <?php
            // Like logic
            $liked = false;
            $tid = $row['id'];
            $likeCheck = $conn->prepare("SELECT id FROM likes WHERE user_id=? AND tweet_id=?");
            $likeCheck->bind_param("ii", $user_id, $tid);
            $likeCheck->execute();
            $likeCheck->store_result();
            $liked = $likeCheck->num_rows > 0;
 
            // Like count
            $likeCountQuery = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE tweet_id=?");
            $likeCountQuery->bind_param("i", $tid);
            $likeCountQuery->execute();
            $likeCount = $likeCountQuery->get_result()->fetch_assoc()['total'];
            ?>
 
            <form action="like.php" method="post" style="display:inline;">
                <input type="hidden" name="tweet_id" value="<?= $tid ?>">
                <button type="submit"><?= $liked ? '❤️' : '🤍' ?> Like (<?= $likeCount ?>)</button>
            </form>
 
            <!-- Comment form -->
            <form method="post" action="" style="margin-top:10px;">
                <input type="hidden" name="tweet_id" value="<?= $tid ?>">
                <input type="text" name="comment" placeholder="Write a comment..." required>
                <button type="submit">Reply</button>
            </form>
 
            <!-- Display comments -->
            <div class="comments">
                <?php
                $comments = $conn->prepare("SELECT comments.content, comments.created_at, users.username 
                                            FROM comments 
                                            JOIN users ON comments.user_id = users.id 
                                            WHERE tweet_id=? ORDER BY comments.created_at ASC");
                $comments->bind_param("i", $tid);
                $comments->execute();
                $commentResult = $comments->get_result();
 
                while ($com = $commentResult->fetch_assoc()): ?>
                    <div class="comment">
                        <strong>@<?= htmlspecialchars($com['username']) ?></strong>: <?= htmlspecialchars($com['content']) ?>
                        <small><?= $com['created_at'] ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <hr>
    <?php endwhile; ?>
</div>
</body>
</html>
 
