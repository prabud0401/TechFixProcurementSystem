<?php
// Check if the required data is provided in the request
if (isset($_POST['supplierID']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['address']) && isset($_POST['password'])) {
    
    $supplierID = $_POST['supplierID'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    // API URL to update the supplier
    $apiUrl = "https://localhost:4000/api/supplier/$supplierID";

    // Prepare the data to be sent in the PUT request
    $supplierData = array(
        'supplierID' => $supplierID,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'password' => $password
    );

    // Convert the data into JSON format
    $jsonData = json_encode($supplierData);

    // Initialize cURL session for PUT request
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors in the request
    if (curl_errno($ch)) {
        echo json_encode(['status' => 'error', 'message' => curl_error($ch)]);
    } else {
        // Decode the API response
        $result = json_decode($response, true);

        // Check the API response for success or error messages
        if (isset($result['message']) && $result['message'] === 'supplier updated successfully!') {
            echo json_encode(['status' => 'success', 'message' => $result['message']]);
        } elseif (isset($result['message']) && $result['message'] === 'supplier ID mismatch.') {
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'An unknown error occurred.']);
        }
    }

    // Close cURL session
    curl_close($ch);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data provided.']);
}
