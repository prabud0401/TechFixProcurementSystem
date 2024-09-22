<?php
// Start the session
session_start();

// Check if customerID is set in the session, if not redirect to login
if (!isset($_SESSION['customerID'])) {
    header("Location: ../log/index.php");
    exit();
}
// Check if quotationCode is already set in the session
if (!isset($_SESSION['quotationCode'])) {
    $_SESSION['quotationCode'] = generateQuotationCode(); // Generate only once per session
}
// Generate a random 12-digit code
function generateQuotationCode() {
    return str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
}
// Get the customerID from the session
$customerID = $_SESSION['customerID'];
$apiBaseUrl = "https://localhost:4000/api/";
$cartApiUrl = "https://localhost:4000/api/cart/";

// Initialize variables to store customer details and messages
$name = $email = $phone = $address = $errorMessage = '';

// Fetch Customer Data
$customerData = fetchCustomerData($apiBaseUrl . "customer/$customerID");
if (isset($customerData['error'])) {
    $errorMessage = $customerData['error'];
} else {
    $name = htmlspecialchars($customerData['name']);
}

// Fetch Categories
$categories = fetchCategories($apiBaseUrl . "Category/");
if (isset($categories['error'])) {
    $errorMessage = $categories['error'];
}

// Fetch Products
$NewProducts = fetchProducts($apiBaseUrl . "NewProduct/");
if (isset($NewProducts['error'])) {
    $errorMessage = $NewProducts['error'];
} else {
    $products = $NewProducts; // Use this to loop through products in HTML
}

/**
 * Function to fetch customer data from the API
 */
function fetchCustomerData($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    } else {
        $result = json_decode($response, true);
        if (is_array($result)) {
            return $result;
        } else {
            return ['error' => "Failed to fetch customer details."];
        }
    }
    curl_close($ch);
}

/**
 * Function to fetch category data from the API
 */
function fetchCategories($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    } else {
        $result = json_decode($response, true);
        if (is_array($result)) {
            return $result;
        } else {
            return ['error' => "Failed to fetch category details."];
        }
    }
    curl_close($ch);
}

/**
 * Function to fetch product data from the API
 */
function fetchProducts($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['error' => "Error: " . curl_error($ch)];
    } else {
        $result = json_decode($response, true);
        if (is_array($result)) {
            return $result;
        } else {
            return ['error' => "Failed to fetch product details."];
        }
    }
    curl_close($ch);
}

// Include header
include("../includes/header.php");
?>

<!-- Navigation -->
<div class="flex w-full space-x-8 justify-center bg-black">    
    <?php include("./nv.php"); ?>
</div>

<section class="h-[90vh] overflow-y-auto">

    <!-- Dashboard HTML Structure -->
    <div class="h-[90vh] bg-gray-900 flex flex-col justify-around items-center text-black px-8 py-8">
    
        <!-- Display error or success message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="text-red-500 text-center mb-4"><?php echo $errorMessage; ?></div>
        <?php else: ?>
                <!-- Customer Details Display -->
                <div class="w-full flex flex-col justify-center items-center">
                    <h2 class="text-center text-2xl font-bold text-gray-500">Welcome, <span class="text-3xl font-extrabold text-white"><?php echo $name; ?></span> </h2>
                    <p class="text-center text-xl font-bold text-gray-500">Customer</p>
                </div>
            <div class="flex w-full justify-end items-end text-black">
                <!-- Category Filter Dropdown -->
                <div class="flex justify-center">
                    <select id="categoryFilter" class="p-2 bg-white border border-gray-300 rounded">
                        <option value="all">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['categoryCode']); ?>"><?php echo htmlspecialchars($category['categoryName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Display Products by Category -->
            <section class="w-full h-full">
                <?php foreach ($categories as $category): ?>
                    <div class="category-section mb-8" data-category-code="<?php echo htmlspecialchars($category['categoryCode']); ?>">
                        <h2 class="text-2xl font-bold text-white mb-4"><?php echo htmlspecialchars($category['categoryName']); ?> (<?php echo htmlspecialchars($category['categoryCode']); ?>)</h2>

                        <div class="grid grid-cols-3 gap-8">
                            <?php
                            $categoryHasProducts = false; // Flag to check if the category has products
                            
                            // Display products that belong to the current category
                            foreach ($products as $product):
                                if ($product['categoryCode'] === $category['categoryCode']): 
                                    $categoryHasProducts = true; // Set flag if a product is found ?>
                                    <div class="w-[300px] h-[420px] bg-white rounded-lg shadow-lg p-4 space-y-4">
                                        <!-- Product Image -->
                                        <div class="w-full h-3/6">
                                            <img src="<?php echo htmlspecialchars($product['imageURL']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
                                        </div>
                                        <div class="w-full h-2/6 overflow-y-auto">
                                            <!-- Product Details -->
                                            <h3 class="text-lg font-bold"><?php echo htmlspecialchars($product['name']); ?></h3>
                                            <p class="text-gray-700"><?php echo htmlspecialchars($product['description']); ?></p>
                                            <p class="text-gray-900 font-bold">$<?php echo htmlspecialchars($product['price']); ?></p>
                                        </div>

                                        <!-- Add to Cart Button -->
                                        <button class="add-to-cart-button w-full h-10 py-2 bg-blue-500 text-white rounded" data-product-name="<?php echo htmlspecialchars($product['name']); ?>" data-product-id="<?php echo $product['newProductID']; ?>">
                                            Add to Cart
                                        </button>
                                    </div>
                                <?php endif;
                            endforeach; ?>

                            <?php if (!$categoryHasProducts): // If no products for this category ?>
                                <p class="text-white">Out of stock</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </div>

    <!-- Modal Structure -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 flex justify-center items-center text-black" id="cartModal">
        <div class="bg-white p-6 rounded-lg w-[400px] h-auto">
            <h2 class="text-2xl font-bold mb-4">Add to Cart</h2>
            <div id="cartProductDetails"></div>
            <form id="addToCartForm" class="space-y-4">
                <!-- Quantity Input -->
                <div>
                    <label for="quantity" class="block font-bold">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" value="1" class="w-full p-2 border rounded" required>
                </div>
                <!-- Urgency Select -->
                <div>
                    <label for="urgency" class="block font-bold">Urgency:</label>
                    <select name="urgency" id="urgency" class="w-full p-2 border rounded">
                        <option value="not-urgent">Not Urgent</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <input type="hidden" name="productId" id="productId">
                <button type="submit" class="w-full py-2 bg-green-500 text-white rounded">Add to Cart</button>
            </form>
            <button id="closeModal" class="w-full py-2 mt-4 bg-red-500 text-white rounded">Close</button>
        </div>
    </div>

</section>

<!-- Include footer -->
<?php include("../includes/footer.php"); ?>

<!-- JavaScript for Modal and Cart Form -->
<script>
    const addToCartButtons = document.querySelectorAll('.add-to-cart-button');
    const cartModal = document.getElementById('cartModal');
    const cartProductDetails = document.getElementById('cartProductDetails');
    const closeModal = document.getElementById('closeModal');
    const productIdInput = document.getElementById('productId');

    // Open modal on Add to Cart button click
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productName = button.getAttribute('data-product-name');
            const productId = button.getAttribute('data-product-id');

            // Set product details in the modal
            cartProductDetails.innerHTML = `<p>Product: <strong>${productName}</strong></p>`;
            productIdInput.value = productId;

            // Show modal
            cartModal.classList.remove('hidden');
        });
    });

    // Close modal on Close button click
    closeModal.addEventListener('click', function () {
        cartModal.classList.add('hidden');
    });

    // Handle form submission for adding to cart
    document.getElementById('addToCartForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Get form values
        const quantity = document.getElementById('quantity').value;
        const urgency = document.getElementById('urgency').value;
        const productId = document.getElementById('productId').value;

        // Send AJAX request to add the item to the cart
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "add_to_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Prepare parameters
        const params = `productId=${productId}&quantity=${quantity}&urgency=${urgency}`;

        // Handle response
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert(xhr.responseText); // Show success or error message
                cartModal.classList.add('hidden'); // Hide modal on success
            } else {
                alert('Error adding to cart.');
            }
        };

        // Send request
        xhr.send(params);
    });
</script>
