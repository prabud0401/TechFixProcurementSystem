<?php
// Start the session
session_start();

// Check if managerID is set in the session, if not redirect to login
if (!isset($_SESSION['managerID'])) {
    header("Location: ../log/index.php");
    exit();
}

// Get the managerID from the session
$managerID = $_SESSION['managerID'];

// Base API URL
$apiUrl = "https://localhost:4000/api";

// Function to fetch order requests by QuotationCode
function getOrderRequestsByQuotationCode($apiUrl, $quotationCode) {
    $url = $apiUrl . "/OrderRequest/byQuotationCode/" . $quotationCode;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode == 404) {
        return ['error' => "No order requests found for the given Quotation Code."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true); 
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Function to delete all order requests by QuotationCode
function deleteOrderRequestsByQuotationCode($apiUrl, $quotationCode) {
    $url = $apiUrl . "/OrderRequest/byQuotationCode/" . $quotationCode;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode == 404) {
        return ['error' => "No order requests found for the given Quotation Code."];
    } elseif ($httpcode == 200) {
        return ['success' => "All order requests deleted successfully!"];
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Function to get the QuotationRequestID by QuotationCode
function getQuotationRequestByQuotationCode($apiUrl, $quotationCode) {
    $url = $apiUrl . "/QuotationRequest/byQuotationCode/" . $quotationCode;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode == 404) {
        return ['error' => "No quotation request found for the given Quotation Code."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Function to delete a quotation request by QuotationRequestID
function deleteQuotationRequest($apiUrl, $quotationRequestID) {
    $url = $apiUrl . "/QuotationRequest/" . $quotationRequestID;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode == 404) {
        return ['error' => "Quotation request not found."];
    } elseif ($httpcode == 200) {
        return ['success' => "Quotation request deleted successfully!"];
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// If a delete request is sent
if (isset($_GET['deleteAll'])) {
    $quotationCode = $_GET['quotationCode'];

    // Fetch QuotationRequestID
    $quotationRequestData = getQuotationRequestByQuotationCode($apiUrl, $quotationCode);
    if (isset($quotationRequestData['error'])) {
        echo "<script>alert('Error: " . $quotationRequestData['error'] . "');</script>";
    } else {
        // Assume there is only one quotation request ID
        $quotationRequestID = $quotationRequestData[0]['quotationRequestID'];

        // Delete all order requests by QuotationCode
        $deleteOrdersResult = deleteOrderRequestsByQuotationCode($apiUrl, $quotationCode);

        if (isset($deleteOrdersResult['success'])) {
            // After deleting all order requests, delete the associated quotation request
            $deleteQuotationResult = deleteQuotationRequest($apiUrl, $quotationRequestID);

            if (isset($deleteQuotationResult['success'])) {
                echo "<script>alert('All order requests and the quotation request were deleted successfully!'); window.location.href = window.location.pathname;</script>";
            } else {
                echo "<script>alert('Error deleting quotation request: " . $deleteQuotationResult['error'] . "');</script>";
            }
        } else {
            echo "<script>alert('Error deleting order requests: " . $deleteOrdersResult['error'] . "');</script>";
        }
    }
}

// Fetch the order requests by QuotationCode
if (isset($_GET['quotationCode'])) {
    $quotationCode = $_GET['quotationCode'];
    $orderRequests = getOrderRequestsByQuotationCode($apiUrl, $quotationCode);
} else {
    $orderRequests = ['error' => "Quotation code is missing."];
}

// Check if the Save button has been clicked for status update
if (isset($_POST['save']) && isset($_POST['quotationRequestID']) && isset($_POST['status']) && isset($_POST['quotationCode']) && isset($_POST['quotationRequestNote'])) {
    $quotationRequestID = $_POST['quotationRequestID'];
    $newStatus = $_POST['status'];
    $quotationCode = $_POST['quotationCode'];
    $quotationRequestNote = $_POST['quotationRequestNote'];

    // Call the update function
    $updateResult = updateQuotationRequestStatus($apiUrl . "/QuotationRequest/", $quotationRequestID, $newStatus, $quotationCode, $quotationRequestNote);

    if (isset($updateResult['success'])) {
        echo "<script>alert('{$updateResult['success']}'); window.location.href = window.location.pathname;</script>";
    } else {
        echo "<script>alert('{$updateResult['error']}');</script>";
    }
}

// Function to update the status of a specific QuotationRequest by ID
function updateQuotationRequestStatus($QuotationRequestApiUrl, $quotationRequestID, $newStatus, $quotationCode, $quotationRequestNote) {
    // Construct the full URL with the quotation request ID
    $url = $QuotationRequestApiUrl . $quotationRequestID;

    // Prepare the data to be updated
    $data = json_encode([
        'quotationRequestID' => $quotationRequestID,
        'quotationCode' => $quotationCode,
        'status' => $newStatus,
        'quotationRequestNote' => $quotationRequestNote
    ]);

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors during the request
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }

    // Get the HTTP status code
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($ch);

    // Handle the response based on the HTTP status code
    if ($httpcode == 200) {
        return ['success' => "Quotation request status updated successfully!"];
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}
?>

<!-- HTML to display the requests -->
<?php include("../includes/header.php"); ?>

<section class="w-full h-screen flex">
    <div class="w-96 h-full">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between pt-16">
        <div class="bg-gray-900 flex flex-col justify-center items-center text-black p-8">
            <!-- Display error or success message -->
            <?php if (isset($orderRequests['error'])): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $orderRequests['error']; ?></div>
            <?php else: ?>
                <?php if (!empty($orderRequests)): ?>
                    <table class="table-auto bg-white text-black w-full mt-4 border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-6 py-3 text-left border-b-2 border-gray-300">Order Request ID</th>
                                <th class="px-6 py-3 text-left border-b-2 border-gray-300">Product Name</th>
                                <th class="px-6 py-3 text-left border-b-2 border-gray-300">Price</th>
                                <th class="px-6 py-3 text-left border-b-2 border-gray-300">Category Code</th>
                                <th class="px-6 py-3 text-left border-b-2 border-gray-300">Quantity</th>
                                <th class="px-6 py-3 text-left border-b-2 border-gray-300">Urgency</th>
                                <th class="px-6 py-3 text-left border-b-2 border-gray-300">Total Price</th>
                                <th class="px-6 py-3 text-left border-b-2 border-gray-300">Added At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderRequests as $request): ?>
                                <tr class="hover:bg-gray-100">
                                    <td class="border px-6 py-4"><?php echo htmlspecialchars($request['orderRequestID']); ?></td>
                                    <td class="border px-6 py-4"><?php echo htmlspecialchars($request['productName']); ?></td>
                                    <td class="border px-6 py-4">$<?php echo number_format($request['price'], 2); ?></td>
                                    <td class="border px-6 py-4"><?php echo htmlspecialchars($request['categoryCode']); ?></td>
                                    <td class="border px-6 py-4"><?php echo htmlspecialchars($request['quantity']); ?></td>
                                    <td class="border px-6 py-4"><?php echo $request['isUrgent'] ? 'Yes' : 'No'; ?></td>
                                    <td class="border px-6 py-4">$<?php echo number_format($request['totalPrice'], 2); ?></td>
                                    <td class="border px-6 py-4"><?php echo htmlspecialchars($request['addedAt']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Update Status -->
                    <form method="POST" class="mt-4">
                        <input type="hidden" name="quotationRequestID" value="<?php echo htmlspecialchars($quotationRequestID); ?>">
                        <input type="hidden" name="quotationCode" value="<?php echo htmlspecialchars($quotationCode); ?>">
                        <label for="status" class="block text-white mb-2">Update Status</label>
                        <select name="status" id="status" class="bg-white border border-gray-300 rounded px-4 py-2 mb-4">
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        <input type="text" name="quotationRequestNote" placeholder="Add a note" class="bg-white border border-gray-300 rounded px-4 py-2 mb-4">
                        <button type="submit" name="save" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
                    </form>

                    <!-- Delete All Button -->
                    <a href="?deleteAll=true&quotationCode=<?php echo htmlspecialchars($quotationCode); ?>" 
                       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 mt-4 inline-block">
                        Delete All
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <!-- Include footer -->
        <?php include("../includes/footer.php"); ?>
    </div>
</section>
