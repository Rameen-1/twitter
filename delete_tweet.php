<?php include '../config.php';
if ($_SESSION['is_admin']) {
    $stmt = $conn->prepare("DELETE FROM tweets WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
}
header("Location: dashboard.php");
 
