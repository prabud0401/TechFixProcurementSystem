<?php
// Start the session
session_start();

// Check if customerID is set in the session
if (!isset($_SESSION['customerID'])) {
    header("Location: ../index.php");
    exit();
}

// Base API URL
$apiUrl = "https://localhost:4000/api";

// Function to fetch QuotationSummary by ID
function getQuotationSummaryById($apiUrl, $quotationSummaryID) {
    $url = $apiUrl . "/QuotationSummary/" . $quotationSummaryID;
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
        return ['error' => "Quotation summary not found."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Function to update pay status
function updatePayStatus($apiUrl, $quotationSummaryID, $payID, $payStatus, $status, $quotationCode, $quotationNote, $customerID, $supplierID, $totalPrice, $quotationPrice) {
    $url = $apiUrl . "/QuotationSummary/" . $quotationSummaryID; // Adjusted to match your API endpoint
    $data = json_encode([
        'quotationSummaryID' => $quotationSummaryID, // Ensure the ID matches
        'quotationCode' => $quotationCode,
        'customerID' => $customerID,
        'supplierID' => $supplierID,
        'totalPrice' => $totalPrice,
        'quotationPrice' => $quotationPrice,
        'quotationNote' => $quotationNote,
        'payID' => $payID,
        'payStatus' => $payStatus, // Update the pay status here
        'status' => $status // Include the status field
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

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $quotationSummaryID = $_GET['id'];
    // Fetch quotation summary details
    $quotationSummary = getQuotationSummaryById($apiUrl, $quotationSummaryID);
} else {
    echo "Quotation Summary ID is missing.";
    exit();
}

// Handle form submission for updating Pay Status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate card payment processing
    $payStatus = "Paid"; // Set to "Paid" after form submission

    // Retrieve the current summary to get the remaining fields
    $payID = $quotationSummary['payID'];
    $quotationCode = $quotationSummary['quotationCode'];
    $quotationNote = $quotationSummary['quotationNote'];
    $customerID = $quotationSummary['customerID'];
    $supplierID = $quotationSummary['supplierID'];
    $totalPrice = $quotationSummary['totalPrice'];
    $quotationPrice = $quotationSummary['quotationPrice'];
    $status = $quotationSummary['status']; // Get the current status

    // Update the pay status using the function
    $payStatusUpdated = updatePayStatus($apiUrl, $quotationSummaryID, $payID, $payStatus, $status, $quotationCode, $quotationNote, $customerID, $supplierID, $totalPrice, $quotationPrice);

    if ($payStatusUpdated) {
        echo "Pay Status updated successfully!";
        // Redirect or perform other actions as needed
    } else {
        echo "Failed to update the Pay Status.";
    }
}

?>

<!-- HTML to display the Quotation Summary details -->
<?php include("../includes/header.php"); ?>

<section class="w-full h-screen flex bg-gray-100">
    <div class="w-96 h-full bg-gray-800 text-white">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between">
        <div class="max-w-6xl mx-auto p-8 space-y-16 overflow-y-auto h-[90vh] text-black">
            <h2 class="text-2xl font-bold text-gray-700">Update Pay Status</h2>

            <!-- Display error message if exists -->
            <?php if (isset($quotationSummary['error'])): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $quotationSummary['error']; ?></div>
            <?php else: ?>
                <?php if (!empty($quotationSummary)): ?>
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block text-gray-700">Quotation Summary ID:</label>
                            <input type="text" name="quotationSummaryID" value="<?php echo htmlspecialchars($quotationSummary['quotationSummaryID']); ?>" class="border p-2 rounded-md" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Quotation Code:</label>
                            <input type="text" name="quotationCode" value="<?php echo htmlspecialchars($quotationSummary['quotationCode']); ?>" class="border p-2 rounded-md" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Pay ID:</label>
                            <input type="text" name="payID" value="<?php echo htmlspecialchars($quotationSummary['payID']); ?>" class="border p-2 rounded-md" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Current Pay Status:</label>
                            <input type="text" name="currentPayStatus" value="<?php echo htmlspecialchars($quotationSummary['payStatus']); ?>" class="border p-2 rounded-md" readonly>
                        </div>
                        <!-- Trigger the modal -->
                        <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 mt-2" data-modal-toggle="paymentModal">
                            Make Payment
                        </button>
                    </form>

                    <!-- Modal for Payment -->
                    <div id="paymentModal" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-gray-900 bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                            <h3 class="text-lg font-bold">Card Payment</h3>
                            <form method="POST" id="paymentForm">
                                <div class="mb-4">
                                    <label class="block text-gray-700">Card Number:</label>
                                    <input type="text" class="border p-2 rounded-md" placeholder="1234 5678 9012 3456" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700">Card Expiry:</label>
                                    <input type="text" class="border p-2 rounded-md" placeholder="MM/YY" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700">CVV:</label>
                                    <input type="text" class="border p-2 rounded-md" placeholder="123" required>
                                </div>
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md shadow hover:bg-green-600 mt-2" onclick="submitPayment(event)">Pay Now</button>
                            </form>
                            <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded-md" onclick="closeModal()">Cancel</button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center text-gray-700">No details available for this quotation summary.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    function closeModal() {
        document.getElementById("paymentModal").classList.add("hidden");
    }

    function submitPayment(event) {
        event.preventDefault(); // Prevent default form submission
        closeModal(); // Close the modal
        
        // You can add additional logic here if needed
        // After closing the modal, the pay status will be updated automatically since the form is already submitted
        document.querySelector('form').submit(); // Submit the form to update pay status
    }

    // Open the modal when the button is clicked
    document.querySelectorAll('[data-modal-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById("paymentModal").classList.toggle("hidden");
        });
    });
</script>

<?php include("../includes/footer.php"); ?>
