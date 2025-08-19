<?php
$conn = new mysqli("localhost", "ux7oqwxcx8vsf", "v3hxvatbehaf", "dbjv2w9cy93tby");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
