<?php
// Start the session
session_start();

// Check if managerID is set in the session
if (!isset($_SESSION['managerID'])) {
    header("Location: ../index.php");
    exit();
}

// Get the managerID from the session
$managerID = $_SESSION['managerID'];

// Base API URL
$apiUrl = "https://localhost:4000/api";

// Function to fetch all QuotationSummaries
function getQuotationSummaries($apiUrl) {
    $url = $apiUrl . "/QuotationSummary"; // Fetch all summaries
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
        return ['error' => "No quotation summaries found."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Fetch all QuotationSummaries
$quotationSummaries = getQuotationSummaries($apiUrl);
?>

<!-- HTML to display the Quotation Summaries -->
<?php include("../includes/header.php"); ?>

<section class="w-full h-screen flex bg-gray-100">
    <div class="w-96 h-full bg-gray-800 text-white">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between">
        <div class="max-w-6xl mx-auto p-8 space-y-16 overflow-y-auto h-[90vh] text-black">
            <h2 class="text-2xl font-bold text-gray-700">All Quotation Summaries</h2>

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
                                    <th class="px-4 py-3 text-left">Current Status</th>
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
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['payStatus']); ?></td>
                                        <td class="px-4 py-3 border">$<?php echo number_format($summary['quotationPrice'], 2); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($summary['status']); ?></td>
                                        <td class="px-4 py-3 border">
                                            <a href="./OrderRequest.php?quotationCode=<?php echo htmlspecialchars($summary['quotationCode']); ?>" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
                                                View
                                            </a>
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
