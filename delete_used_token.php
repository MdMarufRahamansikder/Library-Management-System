<?php
// Read the JSON file
$json = file_get_contents('used_tokens.json');
$data = json_decode($json, true);

// Check if index is sent via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $index = $input['index'];

    // Ensure the index is valid
    if (isset($data['used_tokens'][$index])) {
        // Remove the token from the array
        array_splice($data['used_tokens'], $index, 1);

        // Save the updated data back to the JSON file
        file_put_contents('used_tokens.json', json_encode($data));

        // Respond with success
        echo json_encode(['success' => true]);
    } else {
        // Respond with an error
        echo json_encode(['success' => false, 'message' => 'Invalid token index']);
    }
}
?>
