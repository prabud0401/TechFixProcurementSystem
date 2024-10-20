<?php
// Start the session
session_start();

// Check if supplierID is set in the session
if (!isset($_SESSION['supplierID'])) {
    header("Location: ./index.php");
    exit();
}

// Get the supplierID from the session
$supplierID = $_SESSION['supplierID'];

// Base API URL
$apiUrl = "https://localhost:4000/api";

// Function to fetch quotation request by QuotationCode
function getQuotationRequestByCode($apiUrl, $quotationCode) {
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
        return ['error' => "Quotation request not found."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

// Function to fetch customer details by customerID
function getCustomerDetailsById($apiUrl, $customerID) {
    $url = $apiUrl . "/customer/" . $customerID;
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
        return ['error' => "Customer not found."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true); 
    } else {
        return ['error' => "Unexpected response from the server: HTTP $httpcode"];
    }
}

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

// Handle form submission
$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quotationCode = $_POST['quotationCode'];
    $quotationRequestID = $_POST['quotationRequestID'];
    $quotationPrice = $_POST['quotationPrice'];
    $quotationNote = $_POST['quotationNote'];

    // Generate a random PayID using uniqid
    $payID = uniqid('pay_'); // Generate unique PayID prefixed with 'pay_'
    $payStatus = "Not Paid"; // Payment status set as "Not Paid"

    // Prepare data to send to the API
    $postData = [
        'QuotationCode' => $quotationCode,
        'QuotationNote' => $quotationNote,
        'CustomerID' => $_POST['customerID'],
        'SupplierID' => $supplierID,
        'PayID' => $payID,
        'PayStatus' => $payStatus,
        'QuotationPrice' => $quotationPrice,
        'Status' => 'Pending',
    ];

    // Send data to the QuotationSummary API
    $apiUrl = "https://localhost:4000/api/QuotationSummary";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    // Execute API call
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 201) {
        $successMessage = "Quotation submitted successfully!";
    } else {
        // Log error response for debugging
        echo "Error submitting quotation. Response code: " . $httpcode . ". Response: " . $response;
    }
}

// Fetch quotation request and order requests
if (isset($_GET['quotationCode'])) {
    $quotationCode = $_GET['quotationCode'];
    $quotationRequest = getQuotationRequestByCode($apiUrl, $quotationCode);
    $orderRequests = getOrderRequestsByQuotationCode($apiUrl, $quotationCode);

    if (!isset($orderRequests['error']) && !empty($orderRequests)) {
        $customerID = $orderRequests[0]['customerID'];
        $customerDetails = getCustomerDetailsById($apiUrl, $customerID);
        $totalPrice = array_sum(array_column($orderRequests, 'totalPrice'));
    }
}
?>

<!-- HTML to display the requests -->
<?php include("../includes/header.php"); ?>

<section class="w-full h-screen flex bg-gray-100">
    <div class="w-96 h-full bg-gray-800 text-white">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between"> 
        <div class="max-w-6xl mx-auto p-8 space-y-16 overflow-y-auto h-[90vh] text-black">
            
            <!-- Display success message if set -->
            <?php if ($successMessage): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <strong><?php echo $successMessage; ?></strong>
                </div>
            <?php endif; ?>

            <!-- Display customer details -->
            <?php if (isset($customerDetails) && !isset($customerDetails['error'])): ?>
                <div class="w-full bg-white p-6 rounded-lg shadow-md mb-8">
                    <h3 class="text-2xl font-semibold mb-4 text-gray-700">Customer Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($customerDetails['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customerDetails['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($customerDetails['phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($customerDetails['address']); ?></p>
                    </div>
                </div>
            <?php elseif (isset($customerDetails['error'])): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $customerDetails['error']; ?></div>
            <?php endif; ?>

            <!-- Display order requests -->
            <?php if (isset($orderRequests['error'])): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $orderRequests['error']; ?></div>
            <?php else: ?>
                <?php if (!empty($orderRequests)): ?>
                    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                        <h3 class="text-xl font-semibold mb-4 text-gray-700">Order Requests</h3>
                        <table class="w-full bg-white text-gray-700 rounded-lg shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left">Order Request ID</th>
                                    <th class="px-4 py-3 text-left">Product Name</th>
                                    <th class="px-4 py-3 text-left">Price</th>
                                    <th class="px-4 py-3 text-left">Category Code</th>
                                    <th class="px-4 py-3 text-left">Quantity</th>
                                    <th class="px-4 py-3 text-left">Urgency</th>
                                    <th class="px-4 py-3 text-left">Total Price</th>
                                    <th class="px-4 py-3 text-left">Added At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderRequests as $request): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($request['orderRequestID']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($request['productName']); ?></td>
                                        <td class="px-4 py-3 border">$<?php echo number_format($request['price'], 2); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($request['categoryCode']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($request['quantity']); ?></td>
                                        <td class="px-4 py-3 border"><?php echo $request['isUrgent'] ? 'Yes' : 'No'; ?></td>
                                        <td class="px-4 py-3 border">$<?php echo number_format($request['totalPrice'], 2); ?></td>
                                        <td class="px-4 py-3 border"><?php echo htmlspecialchars($request['addedAt']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Quotation form -->
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-700">Quotation Details</h3>
                        
                        <!-- Total Price Display -->
                        <div class="mb-4">
                            <h4 class="text-lg font-bold text-gray-700">Total Price for All Requests: $<?php echo number_format($totalPrice, 2); ?></h4>
                        </div>

                        <form class="w-full space-y-4" method="POST">
                            <!-- Display related quotation details -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <p><strong>Quotation Code:</strong> <?php echo htmlspecialchars($quotationRequest[0]['quotationCode']); ?></p>
                                <p><strong>Request Date:</strong> <?php echo htmlspecialchars($quotationRequest[0]['requestDate']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($quotationRequest[0]['status']); ?></p>
                                <p><strong>Note:</strong> <?php echo htmlspecialchars($quotationRequest[0]['quotationRequestNote']); ?></p>
                            </div>

                            <!-- Quotation Price -->
                            <div class="flex items-center space-x-2">
                                <label for="quotationPrice" class="font-medium">Quotation Price</label>
                                <input type="text" id="quotationPrice" name="quotationPrice" class="border p-2 w-36 rounded-md" required>
                            </div>

                            <!-- Additional Note -->
                            <div class="flex flex-col">
                                <label for="quotationNote" class="font-medium mb-2">Additional Note</label>
                                <textarea id="quotationNote" name="quotationNote" class="border p-2 rounded-md w-full h-32"></textarea>
                            </div>

                            <!-- Hidden fields -->
                            <input type="hidden" name="quotationCode" value="<?php echo htmlspecialchars($quotationRequest[0]['quotationCode']); ?>">
                            <input type="hidden" name="quotationRequestID" value="<?php echo htmlspecialchars($quotationRequest[0]['quotationRequestID']); ?>">
                            <input type="hidden" name="customerID" value="<?php echo htmlspecialchars($customerID); ?>">

                            <!-- Submit button -->
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md shadow hover:bg-blue-600">
                                Submit Quotation
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <!-- Include footer -->
        <?php include("../includes/footer.php"); ?>
    </div>
</section>
