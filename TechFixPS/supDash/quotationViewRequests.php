<?php
// Start the session
session_start();

// Include header
include("../includes/header.php");

// API URL for fetching QuotationRequest data
$QuotationRequestApiUrl = "https://localhost:4000/api/QuotationRequest/";

// Function to fetch QuotationRequest data from the API
function getQuotationRequests($QuotationRequestApiUrl) {
    // Initialize cURL session
    $ch = curl_init($QuotationRequestApiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
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
        return json_decode($response, true); // Return the list of QuotationRequests as an array
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Function to delete a specific QuotationRequest by ID
function deleteQuotationRequest($QuotationRequestApiUrl, $quotationRequestID) {
    // Construct the full URL
    $url = $QuotationRequestApiUrl . $quotationRequestID;

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
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
        return ['success' => "Quotation request deleted successfully!"];
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Check if a delete request has been made
if (isset($_POST['delete']) && isset($_POST['quotationRequestID'])) {
    $quotationRequestID = $_POST['quotationRequestID'];
    $deleteResult = deleteQuotationRequest($QuotationRequestApiUrl, $quotationRequestID);

    if (isset($deleteResult['success'])) {
        echo "<script>alert('{$deleteResult['success']}'); window.location.href = window.location.pathname;</script>";
    } else {
        echo "<script>alert('{$deleteResult['error']}');</script>";
    }
}

// Fetch the QuotationRequests data from the API
$quotationRequests = getQuotationRequests($QuotationRequestApiUrl);

// Check if the Save button has been clicked
if (isset($_POST['save']) && isset($_POST['quotationRequestID']) && isset($_POST['status']) && isset($_POST['quotationCode']) && isset($_POST['quotationRequestNote'])) {
    $quotationRequestID = $_POST['quotationRequestID'];
    $newStatus = $_POST['status'];
    $quotationCode = $_POST['quotationCode'];
    $quotationRequestNote = $_POST['quotationRequestNote'];

    // Call the update function
    $updateResult = updateQuotationRequestStatus($QuotationRequestApiUrl, $quotationRequestID, $newStatus, $quotationCode, $quotationRequestNote);

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

// Function to download CSV
function downloadCSV($quotationCode) {
    $filePath = '../cusdash/csv/' . htmlspecialchars($quotationCode) . '.csv';
    
    if (file_exists($filePath)) {
        // Set headers to initiate file download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "<script>alert('CSV file not found!');</script>";
    }
}

// Check if download request is sent
if (isset($_GET['downloadCSV'])) {
    downloadCSV($_GET['downloadCSV']);
}
?>

<section class="w-full h-[100vh] flex">
    <div class="w-96 h-full">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between text-black space-y-8 pt-8">
        <!-- Error Message -->
        <?php if (!empty($quotationRequests['error'])): ?>
            <div class="text-red-500 text-center"><?php echo $quotationRequests['error']; ?></div>
        <?php endif; ?>

        <div class="h-full overflow-y-auto px-8 flex flex-col justify-start space-y-8">
            <!-- Display table if there are no errors -->
            <?php if (empty($quotationRequests['error']) && !empty($quotationRequests)): ?>
                <table class="table-auto bg-white text-black w-full mt-4 border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Quotation Request ID</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Quotation Code</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Customer ID</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Status</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Request Date</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Note</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($quotationRequests as $request): ?>
    <tr class="hover:bg-gray-100">
        <td class="border px-6 py-4"><?php echo htmlspecialchars($request['quotationRequestID']); ?></td>
        <td class="border px-6 py-4"><?php echo htmlspecialchars($request['quotationCode']); ?></td>
        <td class="border px-6 py-4"><?php echo htmlspecialchars($request['customerID']); ?></td>
        <td class="border px-6 py-4">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="quotationRequestID" value="<?php echo $request['quotationRequestID']; ?>">
                <input type="hidden" name="quotationCode" value="<?php echo $request['quotationCode']; ?>">
                <input type="hidden" name="quotationRequestNote" value="<?php echo $request['quotationRequestNote']; ?>">

                <select name="status" class="bg-white border border-gray-300 rounded px-4 py-2">
                    <option value="Pending" <?php echo $request['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?php echo $request['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="Rejected" <?php echo $request['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    <option value="Cancelled" <?php echo $request['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="Delivered" <?php echo $request['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="Returned" <?php echo $request['status'] == 'Returned' ? 'selected' : ''; ?>>Returned</option>
                </select>
                <button type="submit" name="save" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
            </form>
        </td>
        <td class="border px-6 py-4"><?php echo htmlspecialchars($request['requestDate']); ?></td>
        <td class="border px-6 py-4"><?php echo htmlspecialchars($request['quotationRequestNote']); ?></td>
        <td class="border px-6 py-4">
        <a href="./OrderRequest.php?quotationCode=<?php echo htmlspecialchars($request['quotationCode']); ?>" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
                        View
                    </a>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="quotationRequestID" value="<?php echo $request['quotationRequestID']; ?>">
                <button type="submit" name="delete" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">Delete</button>
            </form>
            <a href="?downloadCSV=<?php echo htmlspecialchars($request['quotationCode']); ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 ml-2">
                        Download CSV
                    </a>
        </td>
    </tr>
<?php endforeach; ?>

                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="h-[5vh]">
            <!-- Include footer -->
            <?php include("../includes/footer.php"); ?>
        </div>
    </div>
</section>
