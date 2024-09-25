<?php
// Start the session
session_start();

// Check if customerID is set in the session, if not redirect to login
if (!isset($_SESSION['customerID'])) {
    header("Location: ../log/index.php");
    exit();
}

// Get the customerID from the session
$customerID = $_SESSION['customerID'];

// Base API URL
$apiUrl = "https://localhost:4000/api";

// Function to fetch quotation requests by customer ID
function getQuotationRequestsByCustomerID($apiUrl, $customerID) {
    $url = $apiUrl . "/QuotationRequest/byCustomerID/" . $customerID;
    
    // Initialize cURL
    $ch = curl_init($url);
    
    // Set options for cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost

    // Execute the cURL request and get the response
    $response = curl_exec($ch);

    // Check if there was an error with the cURL request
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }

    // Get the HTTP status code of the response
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($ch);

    // Handle response based on the status code
    if ($httpcode == 404) {
        return ['error' => "No quotation requests found for the given Customer ID."];
    } elseif ($httpcode == 200) {
        // Return the response data
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: $httpcode"];
    }
}

// Function to delete a quotation request by ID
function deleteQuotationRequest($apiUrl, $quotationRequestID) {
    // Define the full API endpoint for deleting the quotation request
    $url = $apiUrl . "/QuotationRequest/" . $quotationRequestID;

    // Initialize cURL session
    $ch = curl_init($url);

    // Set options for the cURL session
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost if needed

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check if there was an error during the request
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }

    // Get the HTTP status code of the response
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($ch);

    // Handle the response based on the status code
    if ($httpcode == 404) {
        return ['error' => "Quotation request not found."];
    } elseif ($httpcode == 200) {
        // Successful deletion
        return ['success' => true];
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Check if the delete request is sent
if (isset($_GET['deleteQuotationID'])) {
    $deleteResult = deleteQuotationRequest($apiUrl, $_GET['deleteQuotationID']);
    if (isset($deleteResult['success'])) {
        // If successful, refresh the page
        echo "<script>alert('Quotation request deleted successfully!'); window.location.href = window.location.pathname;</script>";
    } else {
        // If an error occurred, display it
        echo "<script>alert('Error: " . $deleteResult['error'] . "');</script>";
    }
}

// Function to download CSV
function downloadCSV($quotationCode) {
    $filePath = './csv/' . htmlspecialchars($quotationCode) . '.csv';
    
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

// Fetch quotation requests
$quotationRequests = getQuotationRequestsByCustomerID($apiUrl, $customerID);
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
            <?php if (isset($quotationRequests['error'])): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $quotationRequests['error']; ?></div>
            <?php else: ?>
                <?php if (!empty($quotationRequests)): ?>
                    <table class="table-auto bg-white text-black w-full mt-4 border-collapse">
    <thead>
        <tr class="bg-gray-200">
            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Quotation Request ID</th>
            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Quotation Code</th>
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
                <td class="border px-6 py-4"><?php echo htmlspecialchars($request['status']); ?></td>
                <td class="border px-6 py-4"><?php echo htmlspecialchars($request['requestDate']); ?></td>
                <td class="border px-6 py-4"><?php echo htmlspecialchars($request['quotationRequestNote']); ?></td>
                <td class="border px-6 py-4">
                    <!-- View Button -->
                    <a href="./OrderRequest.php?quotationCode=<?php echo htmlspecialchars($request['quotationCode']); ?>" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
                        View
                    </a>
                    <!-- Download CSV Button -->
                    <a href="?downloadCSV=<?php echo htmlspecialchars($request['quotationCode']); ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 ml-2">
                        Download CSV
                    </a>
                    <!-- Delete Button -->
                    <!-- <a href="?deleteQuotationID=<?php echo htmlspecialchars($request['quotationRequestID']); ?>" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 ml-2">
                        Delete
                    </a> -->
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

                <?php endif; ?>
            <?php endif; ?>
        </div>
        <!-- Include footer -->
        <?php include("../includes/footer.php"); ?>
    </div>
</section>
