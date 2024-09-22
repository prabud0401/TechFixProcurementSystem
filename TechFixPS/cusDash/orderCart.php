<?php
// Start the session
session_start();

// Check if customerID is set in the session, if not redirect to login
if (!isset($_SESSION['customerID'])) {
    header("Location: ../log/index.php");
    exit();
}

// Get the customerID and quotationCode from the session
$customerID = $_SESSION['customerID'];
$quotationCode = $_SESSION['quotationCode'];

// Function to fetch product details by NewProductID
function getNewProduct($apiUrl, $newProductID) {
    // Construct the full URL
    $url = $apiUrl . "/NewProduct/" . $newProductID;

    // Initialize cURL
    $ch = curl_init($url);
    
    // Set options for the cURL request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost

    // Execute the request and get the response
    $response = curl_exec($ch);

    // Check for any errors during the request
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }

    // Get the HTTP status code of the response
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($ch);

    // Handle the response based on the status code
    if ($httpcode == 404) {
        return ['error' => "Product not found."];
    } elseif ($httpcode == 200) {
        // Return the product details as an associative array
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: $httpcode"];
    }
}

// Function to fetch cart data
function getCartsByQuotationCode($quotationCode) {
    $url = "https://localhost:4000/api/Cart/byQuotationCode/" . $quotationCode;
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
        return ['error' => "No carts found for the given Quotation Code."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: $httpcode"];
    }
}

// Fetch cart data
$cartData = getCartsByQuotationCode($quotationCode);

// Base API URL
$apiUrl = "https://localhost:4000/api";

// Fetch product details for each cart item
$productDetails = [];
if (!isset($cartData['error'])) { // Only proceed if there is no error in cart data
    foreach ($cartData as $cart) {
        $productInfo = getNewProduct($apiUrl, $cart['newProductID']);
        if (isset($productInfo['name'])) {
            $productDetails[$cart['newProductID']] = [
                'name' => $productInfo['name'],
                'price' => $productInfo['price'],
                'categoryCode' => $productInfo['categoryCode']
            ];
        } else {
            $productDetails[$cart['newProductID']] = [
                'name' => "Unknown Product",
                'price' => 0,
                'categoryCode' => "Unknown"
            ];
        }
    }
}

// Function to edit a cart
function editCart($cartID, $quotationCode, $customerID, $newProductID, $quantity, $isUrgent, $addedAt) {
    $url = "https://localhost:4000/api/Cart/" . $cartID;
    $data = [
        "cartID" => $cartID,
        "quotationCode" => $quotationCode,
        "customerID" => $customerID,
        "newProductID" => $newProductID,
        "quantity" => $quantity,
        "isUrgent" => $isUrgent,
        "addedAt" => $addedAt
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode == 400) {
        return ['error' => "Cart ID mismatch."];
    } elseif ($httpcode == 404) {
        return ['error' => "Cart not found."];
    } elseif ($httpcode == 200) {
        return json_decode($response, true);
    } else {
        return ['error' => "Unexpected response from the server: $httpcode"];
    }
}
// Function to delete a cart
function deleteCart($cartID) {
    $url = "https://localhost:4000/api/Cart/" . $cartID;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 404) {
        return ['error' => "Cart not found."];
    } elseif ($httpcode == 200) {
        $result = json_decode($response, true);
        return $result;
    } else {
        return ['error' => "Unexpected response from the server: $httpcode"];
    }
}

// Handle the delete action if a cartID is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cart_id'])) {
    $cartID = $_POST['delete_cart_id'];
    $deleteResult = deleteCart($cartID);
    if (isset($deleteResult['message'])) {
        $successMessage = $deleteResult['message'];
        // Redirect to the same page to refresh the category list after successful deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Make sure the script stops executing after the redirect
    } elseif (isset($deleteResult['error'])) {
        $errorMessage = $deleteResult['error'];
    }
}

// Handle the edit action if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_cart_id'])) {
    $cartID = $_POST['edit_cart_id'];
    $quantity = $_POST['quantity'];
    $isUrgent = $_POST['isUrgent'] === 'urgent' ? true : false;
    $addedAt = $_POST['addedAt']; // Pass the existing added time
    $newProductID = $_POST['newProductID']; // Assume this is fixed and passed from the form
    $editResult = editCart($cartID, $quotationCode, $customerID, $newProductID, $quantity, $isUrgent, $addedAt);
    if (isset($editResult['message'])) {
        $successMessage = $editResult['message'];
        // Redirect to the same page to refresh the category list after successful deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Make sure the script stops executing after the redirect
    } elseif (isset($editResult['error'])) {
        $errorMessage = $editResult['error'];
    }
}
function postQuotationRequest($apiUrl, $quotationCode, $customerID, $quotationRequestNote) {
    // Prepare the request data
    $data = [
        "quotationCode" => $quotationCode,
        "customerID" => $customerID,
        "status" => "Pending",  // Set the status as 'Pending'
        "quotationRequestNote" => $quotationRequestNote
    ];

    // Initialize cURL
    $ch = curl_init($apiUrl . "/QuotationRequest");

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  // Send data as JSON
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification for localhost

    // Execute the cURL request and get the response
    $response = curl_exec($ch);

    // Check if there is an error during the request
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }

    // Get the HTTP status code
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($ch);

    // Handle response based on HTTP status code
    if ($httpcode == 201 || $httpcode == 200) {
        return json_decode($response, true);  // Return the response if successful
    } else {
        return ['error' => "Unexpected response from the server: $httpcode"];
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quotation_request'])) {
    // Get the inputs from the form
    $quotationCode = $_POST['quotationCode'];
    $customerID = $_SESSION['customerID'];  // Assuming customerID is stored in the session
    $quotationRequestNote = $_POST['quotationRequestNote'];

    // Call the function to send the quotation request
    $apiUrl = "https://localhost:4000/api";  // Base API URL
    $result = postQuotationRequest($apiUrl, $quotationCode, $customerID, $quotationRequestNote);

    if (isset($result['error'])) {
        $errorMessage = $result['error'];  // Handle error
    } else {
        $successMessage = "Quotation request submitted successfully!";
        $deleteResult = deleteCartsByQuotationCode($quotationCode);
    }
}

// Function to delete carts by quotationCode
function deleteCartsByQuotationCode($quotationCode) {
    // Construct the full URL for the DELETE request
    $url = "https://localhost:4000/api/byQuotationCode/" . $quotationCode;

    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    
    // Execute the cURL request
    $response = curl_exec($ch);
    
    // Check for any errors during the request
    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    }

    // Get the HTTP status code of the response
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close the cURL session
    curl_close($ch);
    
    // Handle the response based on the status code
    if ($httpcode == 404) {
        return ['error' => "No carts found for the given Quotation Code."];
    } elseif ($httpcode == 200) {
        return ['success' => "Carts deleted successfully!"];
    } else {
        return ['error' => "Unexpected response from the server: $httpcode"];
    }
}

// Include header
include("../includes/header.php");
?>

<section class="w-full h-screen flex">
    <div class="w-96 h-full">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between pt-16">
        <div class="bg-gray-900 flex flex-col justify-center items-center text-black p-8">
            <!-- Display error or success message -->
            <?php if (isset($errorMessage)): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $errorMessage; ?></div>
            <?php elseif (isset($successMessage)): ?>
                <div class="text-green-500 text-center mb-4"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            
            <div>
                <!-- Display the quotationCode -->
                <p class="text-white">Quotation Code: <?php echo $quotationCode; ?></p>
            </div>

            <?php if (isset($cartData['error'])): ?>
                <!-- Display error message if no cart data found -->
                <p class="text-white"><?php echo $cartData['error']; ?></p>
            <?php elseif (empty($cartData)): ?>
                <!-- Display a message when no items are in the cart -->
                <p class="text-white">No items added to the cart.</p>
            <?php else: ?>
                <!-- Table to display cart data -->
                <table class="table-auto bg-white text-black w-full mt-4 border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Product Name</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Price</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Category Code</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Quantity</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Urgency</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Total Price</th>
                            <th class="px-6 py-3 text-left border-b-2 border-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grandTotal = 0; // Initialize grand total
                        foreach ($cartData as $cart): 
                        // Calculate the total price (Price * Quantity)
                        $totalPrice = $productDetails[$cart['newProductID']]['price'] * $cart['quantity']; 
                        $grandTotal += $totalPrice; // Add to grand total
                        ?>
                        <tr class="hover:bg-gray-100">
                            <!-- NewProduct Name -->
                            <td class="border px-6 py-4"><?php echo htmlspecialchars($productDetails[$cart['newProductID']]['name']); ?></td>
                            
                            <!-- Price -->
                            <td class="border px-6 py-4">$<?php echo number_format($productDetails[$cart['newProductID']]['price'], 2); ?></td>
                            
                            <!-- Category Code -->
                            <td class="border px-6 py-4"><?php echo htmlspecialchars($productDetails[$cart['newProductID']]['categoryCode']); ?></td>
                            
                            <!-- Quantity -->
                            <td class="border px-6 py-4">
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($cart['quantity']); ?>" class="border rounded px-2 py-1 w-20 text-center" min="1">
                            </td>

                            <!-- Urgency -->
                            <td class="border px-6 py-4">
                                    <select name="isUrgent" class="border rounded px-2 py-1">
                                        <option value="not-urgent" <?php echo !$cart['isUrgent'] ? 'selected' : ''; ?>>Not Urgent</option>
                                        <option value="urgent" <?php echo $cart['isUrgent'] ? 'selected' : ''; ?>>Urgent</option>
                                    </select>
                                    <input type="hidden" name="edit_cart_id" value="<?php echo $cart['cartID']; ?>">
                                    <input type="hidden" name="newProductID" value="<?php echo $cart['newProductID']; ?>">
                                    <input type="hidden" name="addedAt" value="<?php echo $cart['addedAt']; ?>">
                            </td>

                            <!-- Total Price -->
                            <td class="border px-6 py-4">$<?php echo number_format($totalPrice, 2); ?></td>

                            <!-- Actions (Save and Delete) -->
                            <td class="border px-6 py-4">
                                <div class="flex space-x-4">
                                    <!-- Save Button -->
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                                    
                                    <!-- Delete Button (within the same form) -->
                                    <button type="submit" name="delete_cart_id" value="<?php echo $cart['cartID']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="return confirm('Are you sure you want to delete this cart item?');">Delete</button>
                                </div>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="flex space-x-8 w-full justify-end items-end">
                    <div class="text-white w-72 py-4 space-y-4">
                        <div class="flex space-x-4 w-full justify-center items-center">
                            <p>Grand Total:</p>
                            <p colspan="2" class="text-left font-bold">$<?php echo number_format($grandTotal, 2); ?></p>
                        </div>
                        <!-- Request Quotation Button -->
                        <button onclick="showModal()" class="rounded h-12 w-72 bg-green-500 hover:bg-green-600 ">Request Quotation Now</button>
                    </div>
                </div>

                <!-- Modal Structure -->
<div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 flex justify-center items-center" id="cartModal">
    <div class="bg-white p-6 rounded-lg w-[400px] h-auto">
        <h3 class="text-xl font-bold mb-4">Request Quotation</h3>
        <p class="mb-2"><strong>Quotation Code:</strong> <?php echo $quotationCode; ?></p>
        <p class="mb-2"><strong>Grand Total:</strong> $<?php echo number_format($grandTotal, 2); ?></p>
        <ul class="mb-4">
            <strong>Products:</strong>
            <?php foreach ($cartData as $cart): ?>
                <li><?php echo htmlspecialchars($productDetails[$cart['newProductID']]['name']) . " - " . $cart['quantity']; ?></li>
            <?php endforeach; ?>
        </ul>
        <!-- Input for Note -->
        <form method="POST">
            <textarea name="quotationRequestNote" class="w-full border border-gray-300 rounded-lg p-2 mb-4" placeholder="Add a note..."></textarea>
            <input type="hidden" name="quotationCode" value="<?php echo $quotationCode; ?>">
            <div class="flex justify-between">
                <button class="bg-red-500 text-white px-4 py-2 rounded" onclick="closeModal()">Close</button>
                <button type="submit" name="submit_quotation_request" class="bg-green-500 text-white px-4 py-2 rounded">Submit Request</button>
            </div>
        </form>
    </div>
</div>


                <script>
                    // Function to show the modal
                    function showModal() {
                        document.getElementById('cartModal').classList.remove('hidden');
                    }
                    
                    // Function to close the modal
                    function closeModal() {
                        document.getElementById('cartModal').classList.add('hidden');
                    }
                </script>
            <?php endif; ?>
        </div>
        <!-- Include footer -->
        <?php include("../includes/footer.php"); ?>
    </div>
</section>


