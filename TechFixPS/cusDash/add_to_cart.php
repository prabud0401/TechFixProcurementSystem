<?php
// Start the session
session_start();

// Check if customerID is set in the session, if not redirect to login
if (!isset($_SESSION['customerID'])) {
    echo "Error: Not logged in!";
    exit();
}

// Get the session data
$quotationCode = $_SESSION['quotationCode'];
$customerID = $_SESSION['customerID'];

// Get the data sent from the AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newProductID = isset($_POST['productId']) ? $_POST['productId'] : null;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : null;
    $isUrgent = isset($_POST['urgency']) && $_POST['urgency'] === 'urgent';

    if ($newProductID && $quantity) {
        // Call the addToCart function to add the product to the cart
        $cartApiUrl = "https://localhost:4000/api/cart/";

        // Call the function
        addToCart($quotationCode, $customerID, $newProductID, $quantity, $isUrgent, $cartApiUrl);
    } else {
        echo "Error: Missing product details.";
    }
}

/**
 * Function to add the product to the cart via API
 */
function addToCart($quotationCode, $customerID, $newProductID, $quantity, $isUrgent, $url) {

    // Prepare the data to be sent in the POST request
    $data = [
        "quotationCode" => $quotationCode,
        "customerID" => $customerID,
        "newProductID" => $newProductID,
        "quantity" => $quantity,
        "isUrgent" => $isUrgent,
        "addedAt" => date('Y-m-d\TH:i:s') // current timestamp in ISO 8601 format
    ];

    // Initialize cURL session
    $ch = curl_init($url);

    // Set the options for the POST request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json', // Specify that we are sending JSON data
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost

    // Execute the POST request and get the response
    $response = curl_exec($ch);

    // Check for any errors
    if (curl_errno($ch)) {
        echo "Error: " . curl_error($ch);
        curl_close($ch);
        return;
    }

    // Decode the response (assuming it's JSON)
    $result = json_decode($response, true);

    // Close the cURL session
    curl_close($ch);

    // Check if the response contains an error or success message
    if (isset($result['message'])) {
        if ($result['message'] === "Cart created successfully!") {
            echo "Success: " . $result['message'];
        } else {
            echo "Error: " . $result['message'];
        }
    } else {
        echo "Unexpected response from the API.";
    }
}
?>
