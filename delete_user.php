<?php include '../config.php';
if ($_SESSION['is_admin']) {
    $stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
}
header("Location: dashboard.php");
 
