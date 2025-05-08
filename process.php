<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize error messages
    $errors = [];
    $tokenRequired = false; // Flag to check if token validation is required

    // Check for empty fields
    if (empty($_POST["fullname"])) {
        $errors[] = "Fullname is required.";
    }
    if (empty($_POST["studentID"])) {
        $errors[] = "Student ID is required.";
    }
    if (empty($_POST["studentMail"])) {
        $errors[] = "Student email is required.";
    }
    if (empty($_POST["borrowdate"])) {
        $errors[] = "Borrowing date is required.";
    }
    if (empty($_POST["returndate"])) {
        $errors[] = "Return date is required.";
    }
    if (empty($_POST["booktitle"])) {
        $errors[] = "Book Title is required.";
    }

    // Validate borrowing and return dates
    $borrowDate = $_POST["borrowdate"];
    $returnDate = $_POST["returndate"];

    try {
        $borrowDateTime = new DateTime($borrowDate);
        $returnDateTime = new DateTime($returnDate);
        $interval = $borrowDateTime->diff($returnDateTime);
        $daysDifference = $interval->days;

        if ($borrowDateTime < $returnDateTime) {
            if ($daysDifference > 10) {
                $tokenRequired = true; // Token validation is required for borrowing periods > 10 days
            }
        } else {
            $errors[] = "The borrowing date must be earlier than the return date.";
        }
    } catch (Exception $e) {
        $errors[] = "Invalid date format. Please provide valid dates.";
    }

    // If token validation is required
 // Validate token if required
 if ($tokenRequired) {
    if (!empty($_POST["token"])) {
        $token = $_POST["token"];

        // Load valid tokens
        $validTokensFile = 'token.json';
        if (!file_exists($validTokensFile)) {
            die("Token file not found.");
        }
        $validTokensData = file_get_contents($validTokensFile);
        $validTokens = json_decode($validTokensData, true)['token'] ?? [];

        // Load used tokens
        $usedTokensFile = 'used_tokens.json';
        if (!file_exists($usedTokensFile)) {
            file_put_contents($usedTokensFile, json_encode(['used_tokens' => []]));
        }
        $usedTokensData = file_get_contents($usedTokensFile);
        $usedTokens = json_decode($usedTokensData, true)['used_tokens'] ?? [];

        // Validate token
        if (!in_array($token, $validTokens)) {
            $errors[] = "Invalid Token!";
        } elseif (in_array($token, $usedTokens)) {
            $errors[] = "This token has already been used.";
        } else {
            // Mark token as used
            $usedTokens[] = $token;
            file_put_contents($usedTokensFile, json_encode(['used_tokens' => $usedTokens]));
        }
    } else {
        $errors[] = "A valid token is required for borrowing periods exceeding 10 days.";
    }
} elseif (!empty($_POST["token"])) {
    // If the token is provided but not required
    $errors[] = "Token is not required for borrowing periods of 10 days or less.";
}

    // If no errors related to empty fields, proceed with other validations
    if (empty($errors)) {
        // Validate and format name
        $fullname = ucwords(strtolower($_POST["fullname"]));
        if (!preg_match("/^([A-Z][a-z]+\.?\s)?[A-Z][a-z]+(\s[A-Z][a-z]+)*$/", $fullname)) {
            $errors[] = "Invalid name format. The name can include optional prefixes like 'Md.' or 'Dr.' and should have first and last names starting with capital letters.";
        }

        // Validate student ID
        $studentID = $_POST["studentID"];
        if (!preg_match("/^\d{2}-\d{5}-\d$/", $studentID)) {
            $errors[] = "Invalid ID format. It should be in the format '22-46420-2'.";
        }

        // Validate email
        $studentMail = $_POST["studentMail"];
        if (!preg_match("/^\d{2}-\d{5}-\d@student\.aiub\.edu$/", $studentMail)) {
            $errors[] = "Invalid email format. It should be in the format '22-46420-1@student.aiub.edu'.";
        }

        // Check if the book is already borrowed using cookies
        $bookName = $_POST["booktitle"];
        $cookieName = "borrowed_" . str_replace(' ', '_', strtolower($bookName));

        if (isset($_COOKIE[$cookieName])) {
            $errors[] = "<h3 style='text-align: center; color:red;'>This book is already borrowed by " . "<br>" . $_COOKIE[$cookieName] . "</h3>" . "<br><h3 style='text-align: center; color: blue;'>Please try to book after 10 days.</h3>";
        } else {
            // Set a cookie for the book
            setcookie($cookieName, $fullname, time() + 30); // Cookie expires in 10 seconds
        }
    }

    // If there are errors, display them; if not, display the receipt
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    } else {
        // Receipt HTML with injected data
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Receipt</title>
            <style>
                /* Receipt Container */
                .receipt {
                    max-width: 400px;
                    margin: 20px auto;
                    padding: 20px;
                    border: 2px solid #4CAF50;
                    border-radius: 10px;
                    background-color: #ffffff;
                    font-family: Arial, sans-serif;
                    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
                    color: #333;
                }
                
                /* Header */
                .receipt h2 {
                    text-align: center;
                    color: #4CAF50;
                    margin-bottom: 20px;
                    font-size: 24px;
                    font-weight: bold;
                }
                
                .receipt .thanks {
                    text-align: center;
                    color: #777;
                    font-size: 14px;
                    margin-bottom: 10px;
                }

                /* Receipt Items */
                .item {
                    font-size: 16px;
                    margin: 8px 0;
                    padding: 5px;
                    display: flex;
                    justify-content: space-between;
                    border-bottom: 1px dashed #ddd;
                }
                
                /* Labels and Values */
                .item span {
                    display: inline-block;
                    color: #555;
                }
                .item .label {
                    font-weight: bold;
                    color: #4CAF50;
                }
                .item .value {
                    color: #333;
                }
                
                /* Footer */
                .divider {
                    border-top: 2px dashed #4CAF50;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class="receipt">
                <h2>Borrow Book Receipt</h2>
                <p class="thanks">Thank you for choosing our services!</p>';
                foreach ($_POST as $key => $value) {
                    if (strtolower($key) !== 'submit') {
                        echo '<div class="item"><span class="label">' . ucfirst(str_replace('_', ' ', $key)) . ':</span> <span class="value">' . htmlspecialchars($value) . '</span></div>';
                    }
                }
                echo '<div class="divider"></div>';
        echo '    </div>
        </body>
        </html>';
    }
} else {
    echo "Please submit the form.";
}
?>
