<?php
// Start the session
session_start();

// Include header
include("../includes/header.php");

// API URL for fetching QuotationRequest data
$QuotationRequestApiUrl = "https://localhost:4000/api/QuotationRequest/";
$QuotationSummaryApiUrl = "https://localhost:4000/api/QuotationSummary/byQuotationCode/"; // New API URL for QuotationSummary

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

// Function to check if the QuotationSummary exists for a given quotationCode
function checkQuotationSummaryExists($QuotationSummaryApiUrl, $quotationCode) {
    $url = $QuotationSummaryApiUrl . $quotationCode;
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }
    
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpcode == 200; // Returns true if the quotation summary exists, otherwise false
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
                            <?php 
                            // Check if the quotationCode exists in QuotationSummary
                            $quotationCodeExists = checkQuotationSummaryExists($QuotationSummaryApiUrl, $request['quotationCode']);
                            if ($quotationCodeExists === true) continue; // Skip this row if quotationCode exists
                            ?>
                            <tr class="hover:bg-gray-100">
                                <td class="border px-6 py-4"><?php echo htmlspecialchars($request['quotationRequestID']); ?></td>
                                <td class="border px-6 py-4"><?php echo htmlspecialchars($request['quotationCode']); ?></td>
                                <td class="border px-6 py-4"><?php echo htmlspecialchars($request['customerID']); ?></td>
                                <td class="border px-6 py-4"><?php echo htmlspecialchars($request['status']); ?></td>
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
