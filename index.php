

<?php
session_start();

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

// Query to fetch books from the database
$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>




<!DOCTYPE html>
<html lang="en">
<head>



<script>
function editBook(bookId) {
    // Send AJAX request to fetch book details
    fetch('get_book.php?id=' + bookId)
        .then(response => response.json())
        .then(data => {
            // Populate the box2 form with book details
            document.querySelector('#box2-book-title').value = data.book_title;
            document.querySelector('#box2-author-name').value = data.author_name;
            document.querySelector('#box2-isbn').value = data.ISBN;
            document.querySelector('#box2-quantity').value = data.quantity;
            document.querySelector('#box2-category').value = data.category;
            document.querySelector('#box2-book-id').value = data.id; // Hidden input for the ID
        })
        .catch(error => console.error('Error fetching book data:', error));
}
</script>



<script>
function deleteBook() {
    const bookId = document.querySelector('#box2-book-id').value;
    if (confirm('Are you sure you want to delete this book?')) {
        fetch('delete_book.php?id=' + bookId, { method: 'DELETE' })
            .then(response => response.text())
            .then(data => {
                alert('Book deleted successfully');
                location.reload();
            })
            .catch(error => console.error('Error deleting book:', error));
    }
}
</script>


<script>
function selectToken(tokenValue) {
    // Find the token input field
    const tokenInput = document.querySelector('#token');
    if (tokenInput) {
        // Update its value
        tokenInput.value = tokenValue;
    } else {
        console.error("Token input field not found");
    }
}
</script>


<script>
function deleteUsedToken(index) {
    if (confirm('Are you sure you want to delete this token?')) {
        // Send a request to the backend to delete the token
        fetch('delete_used_token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ index: index }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Token deleted successfully');
                // Remove the token from the UI
                const tokenList = document.querySelector('#used-token-list');
                tokenList.children[index].remove();
            } else {
                alert('Error deleting token: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>






<h1 class="text">BOOK BORROWING MANAGEMENT</h1>

<style>
    .text {
        font-size: 40px;  /* Make the text bigger */
        color: #4682B4;  /* Set the color to a shade of blue */
        font-weight: bold;  /* Make the text bold */
        text-align: center;  /* Center-align the text */
        text-transform: uppercase;  /* Make the text uppercase */
        padding: 20px;  /* Add some space around the text */
        letter-spacing: 2px;  /* Add some spacing between the letters */
        background-color: #f0f8ff;  /* Light background color for contrast */
        border-radius: 8px;  /* Add rounded corners */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);  /* Add a subtle shadow for depth */
    }
</style>
   <div class="image">
   <img src="id.png" alt="MY ID">
    
    <title>Book Borrowing Management system </title>

   </div>
   
    <link rel="stylesheet" href="index1.css">

    
    <div class="main">
 
        <div class="left"> 

        <div style="display: flex; justify-content: center;">
    <ul style="list-style-type: none; padding: 0; width: 80%; margin-top: 20px;" id="used-token-list">
        <?php
        // Read the JSON file
        $json = file_get_contents('used_tokens.json');
        $data = json_decode($json, true);

        // Display tokens
        if (isset($data['used_tokens']) && is_array($data['used_tokens'])) {
            foreach ($data['used_tokens'] as $index => $token) {
                echo "<li style='background-color: #f0f8ff; padding: 12px; margin-bottom: 8px; font-size: 16px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); text-align: center;'>
                        $token
                        <button onclick='deleteUsedToken($index)' style='margin-left: 10px; background-color: #FF4500; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;'>Delete</button>
                      </li>";
            }
        } else {
            echo "<li style='color: #808080; font-size: 16px; text-align: center;'>No used tokens available</li>";
        }
        ?>
    </ul>
</div>



            
        </div>
 
        <div class="middle">
       
 
            <div class="first">
       
               <div class="box1"> 

                     


               <h1 style="text-align: center; color: #4682b4;">BOOK LIST</h1>

    <!-- Scrollable container for the table -->
    <div style="max-height: 300px; overflow-y: scroll;  margin-top: 10px;">
        <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background-color: #f2f2f2;">
                <tr>
                    <th>Book Title</th>
                    <th>Author Name</th>
                    <th>ISBN</th>
                    <th>Quantity</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($book = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($book['book_title']) . "</td>";
            echo "<td>" . htmlspecialchars($book['author_name']) . "</td>";
            echo "<td>" . htmlspecialchars($book['ISBN']) . "</td>";
            echo "<td>" . htmlspecialchars($book['quantity']) . "</td>";
            echo "<td>" . htmlspecialchars($book['category']) . "</td>";
            echo "<td><button style='background-color: green; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;' onclick='editBook(" . $book['id'] . ")'>Edit</button></td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No books available</td></tr>";
    }
    ?>
</tbody>

        </table>
    </div>
 </div>
       
               <div class="box1"> 


               <h1 style="text-align: center; color: teal;">MANAGE BOOKS</h1>
<form id="editBookForm" method="POST" action="update_book.php">
    <input type="hidden" id="box2-book-id" name="id">
    <label for="box2-book-title">Book Title:</label>
    <input type="text" id="box2-book-title" name="book_title" required><br>

    <label for="box2-author-name">Author Name:</label>
    <input type="text" id="box2-author-name" name="author_name" required><br>

    <label for="box2-isbn">ISBN:</label>
    <input type="text" id="box2-isbn" name="ISBN" required><br>

    <label for="box2-quantity">Quantity:</label>
    <input type="number" id="box2-quantity" name="quantity" required><br>

    <label for="box2-category">Category:</label>
    <select id="box2-category" name="category" required>
        <option value="Fiction">Fiction</option>
        <option value="Non-Fiction">Non-Fiction</option>
        <option value="Science">Science</option>
        <option value="Technology">Technology</option>
        <option value="History">History</option>
        <option value="Biography">Biography</option>
        <option value="Art">Art</option>
        <option value="Philosophy">Philosophy</option>
        <option value="Literature">Literature</option>
        <option value="Children">Children</option>
    </select><br>

    <!-- Update button with custom inline CSS -->
    <button type="submit" style="background-color: #007BFF; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Update</button>
    
    <!-- Delete button with custom inline CSS -->
    <button type="button" onclick="deleteBook()" style="background-color: #FF4500; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Delete</button>
</form>



               </div>
       
               
            </div>
       
            <div class="second" >
       
                <div class="box2">
                    
    
                <img src="book4.png" alt="BOOK" height="300px " width="200px">
               
                </div>
       
                <div class="box2"> 
                <img src="book2.png" alt="BOOK" height="300px " width="200px">    
                
                
                </div>
       
                <div class="box2"> 
                    
                <img src="book1.png" alt="BOOK" height="300px " width="200px">
                
                
                </div>
       
               
            </div>
       
            <div class="first" >
       
                <div class="box6">
                 
                <h1 style="text-align: center; color: #4682b4;">ADD A NEW BOOK</h1>

    <form action="addbook.php" method="POST">
        <label for="book_title">Book Title:</label><br>
        <input type="text" id="book_title" name="book_title" required><br><br>

        <label for="author_name">Author Name:</label><br>
        <input type="text" id="author_name" name="author_name" required><br><br>

        <label for="isbn">ISBN:</label><br>
        <input type="text" id="isbn" name="isbn" required><br><br>

        <label for="quantity">Quantity:</label><br>
        <input type="number" id="quantity" name="quantity" required><br><br>
        <label for="category">Category:</label><br>
        <select id="category" name="category" required>
            <option value="Fiction">Fiction</option>
            <option value="Non-Fiction">Non-Fiction</option>
            <option value="Science">Science</option>
            <option value="Technology">Technology</option>
            <option value="History">History</option>
            <option value="Biography">Biography</option>
            <option value="Art">Art</option>
            <option value="Philosophy">Philosophy</option>
            <option value="Literature">Literature</option>
            <option value="Children">Children</option>
        </select><br><br>

        <input type="submit" value="Add Book">
    </form>




                 </div>
             
            </div>    
       
            <div class="third" >
       
                <div class="box3_1"> 

                <h1 style="text-align: center; color: #FF6347;">BORROW BOOK</h1>


                <form action="process.php" method="post">

                <label for="Student Fullname" name="fullname" id="fullname" > Student Fullname :</label>
                <input type="text"  id="fullname"  name="fullname" required> 
                <br>
            
                <label for="Student AIUB ID" name="studentID" id="studentID" > Student  ID :</label>
                <input type="text" name="studentID" id="studentID" required >

                <label for="Student mail" name="studentMail" id="studentMail" > Student Email :</label>
                <input type="email" name="studentMail" id="studentMail" required >
                <br>

                <label for="Book Title" name="booktitle" id="booktitle" > Book Title :</label>
                
<select name="booktitle" id="booktitle" required>
    
    <?php
    // Fetch books from the database
    $sql = "SELECT book_title FROM books";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($book = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($book['book_title']) . '">' . htmlspecialchars($book['book_title']) . '</option>';
        }
    } else {
        echo '<option value="">No books available</option>';
    }
    ?>
</select>
<br>

         <br>

         <label for="Borrowdate" name="borrowdate" id="borrowdate" > Borrowing Date :</label>
                <input type="date" name="borrowdate" id="borrowdate" required>
                <br>
                <label for="token">Token:</label>
<input type="number" name="token" id="token">

                <br>

                <label for="Returndate" name="returndate" id="returndate" > Return Date :</label>
                <input type="date" name="returndate" id="returndate"  required>

                <br>
                <label for="Fees" name="fees" id="fees" > Fees :</label>
                <input type="text" name="fees" id="fees" required>

                <br>
                <label for="Submit" name="Submit" id="Submit" > Submit :</label>
                <input type="submit" name="Submit" id="Submit">




                </form>



                </div>
                <div class="box3_2">

               
                <h2 style="color: #2E86C1; text-align: center;">AVAILABLE TOKENS</h2>

<ul style="list-style-type: none; padding: 0; font-size: 16px;" id="token-list">
    <?php
    // Read the JSON file
    $json = file_get_contents('token.json');
    $data = json_decode($json, true);

    // Display tokens
    if (isset($data['token']) && is_array($data['token'])) {
        foreach ($data['token'] as $token) {
            echo "<li style='background-color: #f0f8ff; padding: 10px; margin: 5px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; cursor: pointer;' onclick='selectToken(\"$token\")'>$token</li>";
        }
    } else {
        echo "<li style='color: #808080; text-align: center;'>No tokens available</li>";
    }
    ?>
</ul>




            





                </div>
       
               
            </div>
 
 
   
        </div>
 
        <div class="right">
          
        <h2 style="text-align: center; color: #4682B4;">Book Availability</h2>
<div id="book-availability" style="padding: 20px; border: 1px solid #ccc; border-radius: 6px; background-color: #f9f9f9; max-width: 500px; margin: 0 auto;">

    <?php
    // Database connection
    $host = 'localhost'; // Update if needed
    $db = 'library'; // Update if needed
    $user = 'root'; // Update if needed
    $pass = ''; // Update if needed

    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        die('<p style="color: red;">Database connection failed: ' . $conn->connect_error . '</p>');
    }

    // Fetch book availability
    $sql = "SELECT 
                b.book_title,
                b.quantity - IFNULL(COUNT(br.booktitle), 0) AS available_copies
            FROM 
                books b
            LEFT JOIN 
                borrowings br ON b.book_title = br.booktitle
            GROUP BY 
                b.book_title";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Loop through the rows and display book title with available copies
        echo '<ul style="list-style-type: none; padding: 0;">';

        while ($row = $result->fetch_assoc()) {
            echo '<li style="padding: 8px; border-bottom: 1px solid #ccc; font-size: 16px;">';
            echo '<strong>' . htmlspecialchars($row['book_title']) . ':</strong> ' . htmlspecialchars($row['available_copies']) . ' available';
            echo '</li>';
        }

        echo '</ul>';
    } else {
        echo '<p style="color: #808080; text-align: center;">No books available in the library.</p>';
    }

    // Close the connection
    $conn->close();
    ?>


<style>
    #book-availability ul {
        padding: 0;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    #book-availability li {
        padding: 8px;
        border-bottom: 1px solid #ddd;
        font-size: 16px;
    }

    #book-availability li:last-child {
        border-bottom: none;
    }

    #book-availability strong {
        color: #4682B4;
    }
</style>

</div>


        
        
</div>




        
    </div>




        </div>
    </div>
    
    
</head>
<body>
    
</body>
</html>