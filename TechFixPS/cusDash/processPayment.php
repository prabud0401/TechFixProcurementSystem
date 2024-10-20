<?php
// Start the session
session_start();

// Check if customerID is set in the session
if (!isset($_SESSION['customerID'])) {
    header("Location: ./index.php");
    exit();
}

// Get the customerID from the session
$customerID = $_SESSION['customerID'];

// Base API URL
$apiUrl = "https://localhost:4000/api";

// Function to fetch QuotationSummaries by CustomerID and filter by "Approved" status
function getApprovedQuotationSummaries($apiUrl, $customerID) {
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
        $summaries = json_decode($response, true);
        
        // Filter only approved statuses
        $approvedSummaries = array_filter($summaries, function($summary) {
            return $summary['status'] === 'Approved';
        });

        return $approvedSummaries;
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Fetch only Approved QuotationSummaries for the customer
$approvedSummaries = getApprovedQuotationSummaries($apiUrl, $customerID);

?>

<!-- HTML to display the Approved Quotation Summaries -->
<?php include("../includes/header.php"); ?>

<section class="w-full h-screen flex bg-gray-100">
    <div class="w-96 h-full bg-gray-800 text-white">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between">
        <div class="w-full p-8 space-y-16 overflow-y-auto h-[90vh] text-black">
            <h2 class="text-2xl font-bold text-gray-700">Approved Quotation Summaries</h2>

            <!-- Display error or success message -->
            <?php if (isset($approvedSummaries['error'])): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $approvedSummaries['error']; ?></div>
            <?php else: ?>
                <?php if (!empty($approvedSummaries)): ?>
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <table class="w-full bg-white text-gray-700 rounded-lg shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left">Quotation Summary ID</th>
                                    <th class="px-4 py-3 text-left">Quotation Code</th>
                                    <th class="px-4 py-3 text-left">Customer ID</th>
                                    <th class="px-4 py-3 text-left">Supplier ID</th>
                                    <th class="px-4 py-3 text-left">Total Price</th>
                                    <th class="px-4 py-3 text-left">Quotation Price</th>
                                    <th class="px-4 py-3 text-left">Quotation Note</th>
                                    <th class="px-4 py-3 text-left">Request Date</th>
                                    <th class="px-4 py-3 text-left">Pay Status</th>
                                    <th class="px-4 py-3 text-left">Pay ID</th>
                                    <th class="px-4 py-3 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($approvedSummaries as $summary): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['quotationSummaryID']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['quotationCode']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['customerID']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['supplierID']); ?></td>
                                        <td class="px-4 py-3 border">$<?php echo number_format($summary['totalPrice'], 2); ?></td>
                                        <td class="px-4 py-3 border">$<?php echo number_format($summary['quotationPrice'], 2); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['quotationNote']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['requestDate']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['payStatus']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['payID']); ?></td>

                                        <!-- Action for Payment -->
                                        <td class="px-4 py-3 border">
                                            <a href="processPayment.php?id=<?php echo htmlspecialchars($summary['quotationSummaryID']); ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600">
                                                Process Payment
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-gray-700">No Approved Quotation Summaries available.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include("../includes/footer.php"); ?>
