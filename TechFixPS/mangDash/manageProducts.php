
<?php
// Start the session
session_start();

// Include header
include("../includes/header.php");

// API URL for posting NewProduct and Category data
$categoryApiUrl = "https://localhost:4000/api/Category/";
$productApiUrl = "https://localhost:4000/api/NewProduct/";

// Initialize variables to store Category, NewProduct details, and messages
$errorMessage = '';
$successMessage = '';
$categories = [];
$NewProducts = [];

// Fetch existing categories
$ch = curl_init($categoryApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
$response = curl_exec($ch);

if (curl_errno($ch)) {
    $errorMessage = "Error: " . curl_error($ch);
} else {
    $result = json_decode($response, true);
    if (is_array($result)) {
        $categories = $result;
    } else {
        $errorMessage = "Failed to fetch Category details.";
    }
}
curl_close($ch);

// Fetch existing products
$ch = curl_init($productApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    $errorMessage = "Error: " . curl_error($ch);
} else {
    $result = json_decode($response, true);
    if (is_array($result)) {
        $NewProducts = $result;
    } else {
        $errorMessage = "Failed to fetch NewProduct details.";
    }
}
curl_close($ch);

// Function to delete a product by its ID
function deleteProduct($productID, $productApiUrl) {
    $deleteUrl = $productApiUrl . $productID;
    $ch = curl_init($deleteUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Execute DELETE request
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return "Error: " . curl_error($ch);
    } else {
        $result = json_decode($response, true);
        if (isset($result['message']) && $result['message'] === "Product deleted successfully!") {
            return "Product deleted successfully!";
        } else {
            return "Failed to delete product. " . htmlspecialchars($result['message']);
        }
    }
    
    curl_close($ch);
}
// Function to delete a category by its ID
function deleteCategory($categoryID, $categoryApiUrl) {
    // Create the URL with the category ID
    $deleteUrl = $categoryApiUrl . $categoryID;
    
    // Initialize cURL session
    $ch = curl_init($deleteUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost

    // Execute DELETE request
    $response = curl_exec($ch);

    // Check for errors in the request
    if (curl_errno($ch)) {
        return "Error: " . curl_error($ch);
    } else {
        // Decode the API response
        $result = json_decode($response, true);
        
        // Check if the API returned a success message
        if (isset($result['message']) && $result['message'] === "Category deleted successfully!") {
            return "Category deleted successfully!";
        } else {
            return "Failed to delete category. " . htmlspecialchars($result['message']);
        }
    }
    
    // Close cURL session
    curl_close($ch);
}

// If a delete request is made
if (isset($_POST['deleteProduct'])) {
    $productID = $_POST['productID'];
    $deleteResponse = deleteProduct($productID, $productApiUrl);
    
    if (strpos($deleteResponse, "successfully")) {
        $successMessage = $deleteResponse;
    } else {
        $errorMessage = $deleteResponse;
    }
}
// If a delete request for a category is made
if (isset($_POST['deleteCategory'])) {
    $categoryID = $_POST['categoryID'];
    $deleteResponse = deleteCategory($categoryID, $categoryApiUrl);

    if (strpos($deleteResponse, "successfully") !== false) {
        $successMessage = $deleteResponse;
        // Redirect to the same page to refresh the category list after successful deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Make sure the script stops executing after the redirect
    } else {
        $errorMessage = $deleteResponse;
    }
}

// Check if the category form has been submitted
if (isset($_POST['submitCategory'])) {
    // Collect form data for Category
    $categoryCode = $_POST['categoryCode'];
    $categoryName = $_POST['categoryName'];

    // Prepare the data for the Category API
    $categoryData = array(
        "categoryCode" => $categoryCode,
        "categoryName" => $categoryName
    );

    // Convert the data into JSON format
    $jsonCategoryData = json_encode($categoryData);

    // Initialize cURL session for the Category POST request
    $ch = curl_init($categoryApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonCategoryData);

    // Execute the cURL request for Category
    $categoryResponse = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        $errorMessage = "Error: " . curl_error($ch);
    } else {
        // Decode the API response for Category
        $categoryResult = json_decode($categoryResponse, true);

        // Check if the API returned a success message
        if (isset($categoryResult['message']) && $categoryResult['message'] === "Category created successfully!") {
            $successMessage = "Category added successfully!";
            // Refresh the page to show updated products
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $errorMessage = "Failed to create category. " . htmlspecialchars($categoryResult['message']);
        }
    }

    // Close cURL session
    curl_close($ch);
}

// Check if the product form has been submitted
if (isset($_POST['submitProduct'])) {
    // Collect form data for Product
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $categoryName = $_POST['categoryName'];
    $categoryCode = $_POST['categoryCode'];
    $imageURL = $_POST['imageURL'];

    // Prepare the data for the Product API
    $productData = array(
        "name" => $name,
        "description" => $description,
        "price" => (float)$price,
        "categoryName" => $categoryName,
        "categoryCode" => $categoryCode,
        "imageURL" => $imageURL
    );

    // Convert the data into JSON format
    $jsonProductData = json_encode($productData);

    // Initialize cURL session for the Product POST request
    $ch = curl_init($productApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonProductData);

    // Execute the cURL request for Product
    $productResponse = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        $errorMessage = "Error: " . curl_error($ch);
    } else {
        // Decode the API response for Product
        $productResult = json_decode($productResponse, true);

        // Check if the API returned a success message
        if (isset($productResult['message']) && $productResult['message'] === "Product created successfully!") {
            $successMessage = "Product added successfully!";
            // Refresh the page to show updated products
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $errorMessage = "Failed to create product. " . htmlspecialchars($productResult['message']);
        }
    }

    // Close cURL session
    curl_close($ch);
}


?>
<?php
function updateProduct($productID, $productData, $productApiUrl) {
    // Create the URL with the product ID
    $putUrl = $productApiUrl . $productID;

    // Initialize cURL for the PUT request
    $ch = curl_init($putUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Convert the data to JSON format
    $jsonProductData = json_encode($productData);

    // Set the PUT data
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonProductData);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        return "Error: " . curl_error($ch);
    } else {
        // Decode the response
        $result = json_decode($response, true);

        // Check if the update was successful
        if (isset($result['message']) && $result['message'] === "Product updated successfully!") {
            return "Product updated successfully!";
        } else {
            return "Failed to update product. " . htmlspecialchars($result['message']);
        }
    }

    // Close cURL session
    curl_close($ch);
}
?>
<?php
if (isset($_POST['submitEditProduct'])) {
    // Collect the form data
    $productID = $_POST['productID'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $categoryName = $_POST['categoryName'];
    $categoryCode = $_POST['categoryCode'];
    $imageURL = $_POST['imageURL'];

    // Prepare the data for the PUT request
    $productData = array(
        "newProductID" => $productID,
        "name" => $name,
        "description" => $description,
        "price" => (float)$price,
        "categoryName" => $categoryName,
        "categoryCode" => $categoryCode,
        "imageURL" => $imageURL
    );

    // Call the updateProduct function to send the PUT request
    $updateResponse = updateProduct($productID, $productData, $productApiUrl);

    // Handle the response
    if (strpos($updateResponse, "successfully") !== false) {
        $successMessage = $updateResponse;
        // Optional: Redirect to avoid resubmission on page refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $errorMessage = $updateResponse;
    }
}

// Rest of the code or script ends here
?>


<section class="w-full h-[100vh] flex">
    <div class="w-96 h-full">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between text-black space-y-8 pt-8">
        <!-- Error Message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="text-red-500 text-center"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <!-- Success Message -->
        <?php if (!empty($successMessage)): ?>
            <div class="text-green-500 text-center"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <div class="h-full  overflow-y-auto px-8 flex flex-col justify-start space-y-8">
            <div class="flex space-x-8 w-full h-[60vh]">
                <!-- Add New Category Section -->
                <div class="w-full p-8 space-y-4 bg-gray-700 rounded-lg shadow-lg h-full">
                    <div class="flex flex-col h-2/6 space-y-4">
                        <h2 class="text-center text-3xl font-extrabold text-white">
                            Add New Category
                        </h2>

                        <form method="POST" action="" class="w-full space-y-8 flex flex-col items-end">
                            <div class="w-full flex space-x-8">
                                <!-- Category Code -->
                                <input type="text" name="categoryCode" placeholder="Category Code" class="w-full p-2 border rounded" required>
                                
                                <!-- Category Name -->
                                <input type="text" name="categoryName" placeholder="Category Name" class="w-full p-2 border rounded" required>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" name="submitCategory" class="px-4 py-2 bg-green-500 text-white rounded w-72">Add Category</button>
                        </form>
                    </div>

                    <!-- Category Table -->
                    <div class="bg-red-100 h-4/6 w-full overflow-y-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="w-full bg-gray-800 text-white">
                                    <th class="py-2 px-4">Category ID</th>
                                    <th class="py-2 px-4">Category Code</th>
                                    <th class="py-2 px-4">Category Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <tr class="border-b">
                                            <td class="py-2 px-4"><?php echo $category['categoryID']; ?></td>
                                            <td class="py-2 px-4"><?php echo $category['categoryCode']; ?></td>
                                            <td class="py-2 px-4"><?php echo $category['categoryName']; ?></td>
                                            <td class="py-2 px-4">
                                                <!-- Delete Button -->
                                                <form method="POST" action="" class="">
                                                    <input type="hidden" name="categoryID" value="<?php echo $category['categoryID']; ?>">
                                                    <button type="submit" name="deleteCategory" class="px-4 py-2 bg-red-500 text-white rounded w-full">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4">No categories available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add New Product Section -->
                <div class="w-full p-8 space-y-4 bg-gray-800 rounded-lg shadow-lg mx-auto">
                    <h2 class="text-center text-3xl font-extrabold text-white">
                        Add New Product
                    </h2>

                    <form method="POST" action="" class="w-full space-y-8 flex flex-col items-end">
                        <div class="w-full space-y-6 flex flex-col items-end">
                            <!-- Name -->
                            <input type="text" name="name" placeholder="Product Name" class="w-full p-2 border rounded" required>

                            <!-- Description -->
                            <textarea name="description" placeholder="Product Description" class="w-full p-2 border rounded" required></textarea>

                            <!-- Price -->
                            <input type="number" name="price" placeholder="Price" class="w-full p-2 border rounded" step="0.01" required>

                            <!-- Category Name Dropdown -->
                            <select name="categoryName" class="w-full p-2 border rounded" required>
                                <option value="" disabled selected>Select Category Name</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['categoryName']; ?>"><?php echo $category['categoryName']; ?></option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Category Code Dropdown -->
                            <select name="categoryCode" class="w-full p-2 border rounded" required>
                                <option value="" disabled selected>Select Category Code</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['categoryCode']; ?>"><?php echo $category['categoryCode']; ?></option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Image URL -->
                            <input type="url" name="imageURL" placeholder="Image URL" class="w-full p-2 border rounded" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" name="submitProduct" class="px-4 py-2 bg-green-500 text-white rounded mt-8">Add Product</button>
                    </form>
                </div>
            </div>

            <!-- Display Products -->
            <div class="bg-gray-900 flex flex-wrap justify-center items-center text-black gap-8 h-full">
                <?php if (!empty($NewProducts)): ?>
                    <?php foreach ($NewProducts as $product): ?>
                        <div class="w-[300px] h-[450px] bg-white rounded-lg shadow-lg p-4 space-y-6">
                            <!-- Product Image -->
                            <div class="w-full h-2/5 overflow-auto">
                                <img src="<?php echo $product['imageURL']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-full object-cover rounded-t-lg">
                            </div>
                            <!-- Product Details -->
                            <div class="h-2/5 overflow-auto">
                                <h3 class="text-lg font-bold"><?php echo $product['name']; ?></h3>
                                <p class="text-sm text-gray-600 mt-2"><?php echo $product['description']; ?></p>
                                <p class="text-lg font-semibold text-green-500 mt-4">$<?php echo number_format($product['price'], 2); ?></p>
                                <p class="text-sm text-gray-500 mt-2">Category: <?php echo $product['categoryName']; ?></p>
                                <p class="text-sm text-gray-500">Category Code: <?php echo $product['categoryCode']; ?></p>
                            </div>

                            <!-- Edit and Delete Buttons -->
                            <div class="w-full flex space-x-4 h-1/5">
                                <!-- Edit Button -->
                                <button class="edit-button bg-blue-500 text-white rounded w-1/2 h-10"
                                        data-product-id="<?php echo $product['newProductID']; ?>"
                                        data-name="<?php echo $product['name']; ?>"
                                        data-description="<?php echo $product['description']; ?>"
                                        data-price="<?php echo $product['price']; ?>"
                                        data-category-name="<?php echo $product['categoryName']; ?>"
                                        data-category-code="<?php echo $product['categoryCode']; ?>"
                                        data-image-url="<?php echo $product['imageURL']; ?>">
                                    Edit
                                </button>

                                <!-- Delete Button -->
                                <form method="POST" action="" class="w-1/2 h-10">
                                    <input type="hidden" name="productID" value="<?php echo $product['newProductID']; ?>">
                                    <button type="submit" name="deleteProduct" class="px-4 py-2 bg-red-500 text-white rounded w-full">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-white">No products available</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="h-[5vh]">
            <!-- Include footer -->
            <?php include("../includes/footer.php"); ?>
        </div>

    </div>
</section>

<!-- JavaScript for handling modal population and display -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-button');
    const editModal = document.getElementById('editModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModal = document.getElementById('closeModal');

    // Loop through edit buttons and add click event
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Get product data from data attributes
            const productID = button.getAttribute('data-product-id');
            const name = button.getAttribute('data-name');
            const description = button.getAttribute('data-description');
            const price = button.getAttribute('data-price');
            const categoryName = button.getAttribute('data-category-name');
            const categoryCode = button.getAttribute('data-category-code');
            const imageURL = button.getAttribute('data-image-url');

            // Populate the modal form with the product data
            document.querySelector('#editModal input[name="productID"]').value = productID;
            document.querySelector('#editModal input[name="name"]').value = name;
            document.querySelector('#editModal textarea[name="description"]').value = description;
            document.querySelector('#editModal input[name="price"]').value = price;
            document.querySelector('#editModal select[name="categoryName"]').value = categoryName;
            document.querySelector('#editModal select[name="categoryCode"]').value = categoryCode;
            document.querySelector('#editModal input[name="imageURL"]').value = imageURL;

            // Display the modal and overlay
            modalOverlay.classList.remove('hidden');
        });
    });

    // Close modal on clicking the close button
    closeModal.addEventListener('click', function () {
        modalOverlay.classList.add('hidden');
    });

    // Optional: Close modal when clicking on overlay itself
    modalOverlay.addEventListener('click', function (e) {
        if (e.target === modalOverlay) {
            modalOverlay.classList.add('hidden');
        }
    });
});

</script>

<!-- Modal Overlay for Background Blur -->
<div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 flex justify-center items-center text-black" id="modalOverlay">
    <!-- EDIT Product Section -->
    <div class="w-1/2 p-8 space-y-4 bg-gray-800 rounded-lg shadow-lg mx-auto" id="editModal">
        <h2 class="text-center text-3xl font-extrabold text-white">
            EDIT Product
        </h2>

        <form method="POST" action="" class="w-full space-y-6 flex flex-col items-end">
            <!-- Hidden field for Product ID -->
            <input type="hidden" name="productID">

            <!-- Product Name -->
            <input type="text" name="name" placeholder="Product Name" required class="w-full p-2 border rounded">

            <!-- Description -->
            <textarea name="description" placeholder="Product Description" required class="w-full p-2 border rounded"></textarea>

            <!-- Price -->
            <input type="number" name="price" placeholder="Price" step="0.01" required class="w-full p-2 border rounded">

            <!-- Category Name Dropdown -->
            <select name="categoryName" class="w-full p-2 border rounded" required>
                <option value="" disabled>Select Category Name</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['categoryName']; ?>" 
                        <?php if (isset($currentCategoryName) && $category['categoryName'] == $currentCategoryName) echo 'selected'; ?>>
                        <?php echo $category['categoryName']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Category Code Dropdown -->
            <select name="categoryCode" class="w-full p-2 border rounded" required>
                <option value="" disabled>Select Category Code</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['categoryCode']; ?>" 
                        <?php if (isset($currentCategoryCode) && $category['categoryCode'] == $currentCategoryCode) echo 'selected'; ?>>
                        <?php echo $category['categoryCode']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Image URL -->
            <input type="url" name="imageURL" placeholder="Image URL" required class="w-full p-2 border rounded">

            <!-- Submit Button -->
            <button type="submit" name="submitEditProduct" class="px-4 py-2 bg-blue-500 text-white rounded">Update Product</button>
        </form>

        <!-- Close Modal Button -->
        <button id="closeModal" class="mt-4 px-4 py-2 bg-red-500 text-white rounded">Close</button>
    </div>
</div>
