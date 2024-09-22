<?php
// Check if the supplierID is provided in the request
if (isset($_POST['supplierID'])) {
    $supplierID = $_POST['supplierID'];

    // API URL for deleting a supplier
    $apiUrl = "https://localhost:4000/api/supplier/$supplierID";

    // Initialize cURL session for DELETE request
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors in the request
    if (curl_errno($ch)) {
        echo json_encode(['status' => 'error', 'message' => curl_error($ch)]);
    } else {
        // Decode the API response
        $result = json_decode($response, true);
        if (isset($result['message']) && $result['message'] === 'supplier deleted successfully!') {
            echo json_encode(['status' => 'success', 'message' => $result['message']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete supplier.']);
        }
    }

    // Close cURL session
    curl_close($ch);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No supplier ID provided.']);
}
