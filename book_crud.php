<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Create Operation
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    $title = $_POST['book_title'];
    $author = $_POST['author_name'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];

    $sql = "INSERT INTO books (book_title, author_name, ISBN, quantity, category) 
            VALUES ('$title', '$author', '$isbn', '$quantity', '$category')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?message=Book added successfully");
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle Update Operation
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['book_id'];
    $title = $_POST['book_title'];
    $author = $_POST['author_name'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];

    $sql = "UPDATE books SET 
                book_title = '$title', 
                author_name = '$author', 
                ISBN = '$isbn', 
                quantity = '$quantity', 
                category = '$category' 
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?message=Book updated successfully");
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle Delete Operation
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    $sql = "DELETE FROM books WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?message=Book deleted successfully");
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
