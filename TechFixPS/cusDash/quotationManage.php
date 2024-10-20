<?php
// Start the session
session_start();

// Check if customerID is set in the session
if (!isset($_SESSION['customerID'])) {
    header("Location: ../index.php");
    exit();
}

// Get the customerID from the session
$customerID = $_SESSION['customerID'];

// Base API URL
$apiUrl = "https://localhost:4000/api";

// Function to fetch QuotationSummary by CustomerID
function getQuotationSummariesByCustomerID($apiUrl, $customerID) {
    $url = $apiUrl . "/QuotationSummary/byCustomerID/" . $customerID;
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
        return ['error' => "No quotation summaries found for the given Customer ID."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Function to update quotation status
function updateQuotationStatus($apiUrl, $quotationSummaryID, $status, $payID, $payStatus, $quotationCode, $quotationNote, $customerID, $supplierID, $totalPrice, $quotationPrice) {
    $url = $apiUrl . "/QuotationSummary/" . $quotationSummaryID; // Adjusted to match your API endpoint
    $data = json_encode([
        'quotationSummaryID' => $quotationSummaryID, // Ensure the ID matches
        'quotationCode' => $quotationCode,
        'customerID' => $customerID,
        'supplierID' => $supplierID,
        'totalPrice' => $totalPrice,
        'quotationPrice' => $quotationPrice,
        'quotationNote' => $quotationNote,
        'status' => $status,
        'payStatus' => $payStatus,
        'payID' => $payID
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Log the response for debugging
    echo "Response Code: $httpcode<br>";
    echo "Response: $response<br>";

    return $httpcode == 200; // Return true if successful, false otherwise
}

// Handle status update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quotationSummaryID'])) {
    $quotationSummaryID = $_POST['quotationSummaryID'];
    $newStatus = $_POST['status'];

    // Retrieve the current summary to get the remaining fields
    $quotationSummaries = getQuotationSummariesByCustomerID($apiUrl, $customerID);
    $currentSummary = array_filter($quotationSummaries, function($summary) use ($quotationSummaryID) {
        return $summary['quotationSummaryID'] == $quotationSummaryID;
    });
    
    if ($currentSummary) {
        $currentSummary = reset($currentSummary); // Get the first matching element
        $payID = $currentSummary['payID'];
        $payStatus = $currentSummary['payStatus'];
        $quotationCode = $currentSummary['quotationCode'];
        $quotationNote = $currentSummary['quotationNote'];
        $customerID = $currentSummary['customerID'];
        $supplierID = $currentSummary['supplierID'];
        $totalPrice = $currentSummary['totalPrice'];
        $quotationPrice = $currentSummary['quotationPrice'];

        // Update status
        $statusUpdated = updateQuotationStatus($apiUrl, $quotationSummaryID, $newStatus, $payID, $payStatus, $quotationCode, $quotationNote, $customerID, $supplierID, $totalPrice, $quotationPrice);
        
        if ($statusUpdated) {
            echo "Status updated successfully!";
            // Refresh the summaries after updating the status
            $quotationSummaries = getQuotationSummariesByCustomerID($apiUrl, $customerID);
        } else {
            echo "Failed to update the status.";
        }
    } else {
        echo "Quotation summary not found.";
    }
}

// Fetch QuotationSummaries for the customer
$quotationSummaries = getQuotationSummariesByCustomerID($apiUrl, $customerID);
?>

<!-- HTML to display the Quotation Summaries -->
<?php include("../includes/header.php"); ?>

<section class="w-full h-screen flex bg-gray-100">
    <div class="w-96 h-full bg-gray-800 text-white">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between">
        <div class="max-w-6xl mx-auto p-8 space-y-16 overflow-y-auto h-[90vh] text-black">
            <h2 class="text-2xl font-bold text-gray-700">Your Quotation Summaries</h2>

            <!-- Display error or success message -->
            <?php if (isset($quotationSummaries['error'])): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $quotationSummaries['error']; ?></div>
            <?php else: ?>
                <?php if (!empty($quotationSummaries)): ?>
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <table class="w-full bg-white text-gray-700 rounded-lg shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left">Quotation Summary ID</th>
                                    <th class="px-4 py-3 text-left">Quotation Code</th>
                                    <th class="px-4 py-3 text-left">Supplier ID</th>
                                    <th class="px-4 py-3 text-left">Pay ID</th>
                                    <th class="px-4 py-3 text-left">Pay Status</th>
                                    <th class="px-4 py-3 text-left">Quotation Price</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quotationSummaries as $summary): ?>
                                    <tr class="hover:bg-gray-50">
    <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['quotationSummaryID']); ?></td>
    <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['quotationCode']); ?></td>
    <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['supplierID']); ?></td>
    <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['payID']); ?></td>
    <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['payStatus']); ?>
        <!-- Show Pay Button if Pay Status is Not Paid -->
        <?php if ($summary['payStatus'] === 'Not Paid'): ?>
            <a href="updatePayStatus.php?id=<?php echo htmlspecialchars($summary['quotationSummaryID']); ?>" class="bg-green-500 text-white px-4 py-2 rounded-md shadow hover:bg-green-600">
                Pay
            </a>
        <?php endif; ?>
    </td>
    <td class="px-4 py-3 border">$<?php echo number_format($summary['quotationPrice'], 2); ?></td>
    <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['status']); ?></td>
    <td class="px-4 py-3 border">
        <form method="POST" class="inline-block">
            <input type="hidden" name="quotationSummaryID" value="<?php echo htmlspecialchars($summary['quotationSummaryID']); ?>">
            <select name="status" class="border p-2 rounded-md">
                <option value="Pending" <?php if ($summary['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Approved" <?php if ($summary['status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                <option value="Rejected" <?php if ($summary['status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 mt-2">
                Save
            </button>
        </form>
    </td>
</tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-gray-700">No Quotation Summaries available.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include("../includes/footer.php"); ?>
