<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection details
    $servername = "localhost";
    $username = "root"; // Default username for MySQL in XAMPP
    $password = ""; // Default password is empty
    $dbname = "library"; // Replace with your actual database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $book_title = $conn->real_escape_string($_POST['book_title']);
    $author_name = $conn->real_escape_string($_POST['author_name']);
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $quantity = (int)$_POST['quantity'];
    $category = $conn->real_escape_string($_POST['category']);

    // Prepare the SQL query to insert the data
    $sql = "INSERT INTO books (book_title, author_name, ISBN, quantity, category) 
            VALUES ('$book_title', '$author_name', '$isbn', $quantity, '$category')";

    // Execute the query and check if it was successful
    if ($conn->query($sql) === TRUE) {
        // Redirect to index.php after successful insertion
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
