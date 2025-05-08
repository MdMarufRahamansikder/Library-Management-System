<?php
$conn = new mysqli("localhost", "root", "", "library");
$id = $_POST['id'];
$title = $_POST['book_title'];
$author = $_POST['author_name'];
$isbn = $_POST['ISBN'];
$quantity = $_POST['quantity'];
$category = $_POST['category'];

$sql = "UPDATE books SET book_title = ?, author_name = ?, ISBN = ?, quantity = ?, category = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssisi", $title, $author, $isbn, $quantity, $category, $id);
$stmt->execute();
$conn->close();
header("Location: index.php");
?>
