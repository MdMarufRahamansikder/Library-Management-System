<?php
$conn = new mysqli("localhost", "root", "", "library");
$id = $_GET['id'];
$sql = "DELETE FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$conn->close();
?>
