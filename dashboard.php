<?php include '../config.php';
if (!$_SESSION['is_admin']) die("Access denied");
 
$tweets = $conn->query("SELECT t.*, u.username FROM tweets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
$users = $conn->query("SELECT * FROM users");
?>
 
<h2>Admin Dashboard</h2>
<a href="../index.php">Back</a>
 
<h3>All Tweets</h3>
<?php while ($tweet = $tweets->fetch_assoc()): ?>
    <p>
        <b>@<?= $tweet['username'] ?>:</b> <?= $tweet['content'] ?>
        <a href="delete_tweet.php?id=<?= $tweet['id'] ?>">🗑️ Delete</a>
    </p>
<?php endwhile; ?>
 
<h3>Users</h3>
<?php while ($user = $users->fetch_assoc()): ?>
    <p>
        <?= $user['username'] ?> (<?= $user['email'] ?>)
        <?php if (!$user['is_admin']): ?>
            <a href="delete_user.php?id=<?= $user['id'] ?>">Deactivate</a>
        <?php endif; ?>
    </p>
<?php endwhile; ?>
 
