<?php
// Start the session
session_start();

// Include header
include("../includes/header.php");
?>

<section class="w-full h-screen flex">
    <div class="w-96 h-full">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-center items-center pt-16">
        <!-- Form for adding new product -->
        <div class="w-full max-w-md p-8 space-y-6 bg-gray-800 rounded-lg shadow-lg">
            <h2 class="text-center text-3xl font-extrabold text-white">
                Add New Product
            </h2>
            
            <!-- Error Message -->
            <div id="errorMessage" class="text-red-500 text-center"></div>
            <!-- Success Message -->
            <div id="successMessage" class="text-green-500 text-center"></div>

            <form id="addProductForm">
                <div class="space-y-4">
                    <!-- Name -->
                    <input type="text" id="name" placeholder="Product Name" class="w-full p-2 border rounded" required>
                    
                    <!-- Description -->
                    <textarea id="description" placeholder="Product Description" class="w-full p-2 border rounded" required></textarea>

                    <!-- Price -->
                    <input type="number" id="price" placeholder="Price" class="w-full p-2 border rounded" step="0.01" required>

                    <!-- Category Name -->
                    <input type="text" id="categoryName" placeholder="Category Name" class="w-full p-2 border rounded" required>

                    <!-- Category Code -->
                    <input type="text" id="categoryCode" placeholder="Category Code" class="w-full p-2 border rounded" required>

                    <!-- Image URL -->
                    <input type="url" id="imageURL" placeholder="Image URL" class="w-full p-2 border rounded" required>
                </div>
                
                <!-- Submit Button -->
                <button type="button" class="w-full mt-6 p-2 bg-green-500 text-white rounded" onclick="addProduct()">Add Product</button>
            </form>
        </div>
    </div>
</section>

<script>
// Function to add a new product
function addProduct() {
    // Clear previous messages
    document.getElementById('errorMessage').innerHTML = '';
    document.getElementById('successMessage').innerHTML = '';

    // Collect form data
    var name = document.getElementById('name').value;
    var description = document.getElementById('description').value;
    var price = document.getElementById('price').value;
    var categoryName = document.getElementById('categoryName').value;
    var categoryCode = document.getElementById('categoryCode').value;
    var imageURL = document.getElementById('imageURL').value;

    // Create product object
    var productData = {
        "name": name,
        "description": description,
        "price": parseFloat(price),
        "categoryName": categoryName,
        "categoryCode": categoryCode,
        "imageURL": imageURL
    };

    // Make AJAX request to API
    fetch('https://localhost:4000/api/NewProduct/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(productData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.message === "Product created successfully!") {
            // Success
            document.getElementById('successMessage').innerHTML = data.message;
        } else {
            // Error
            document.getElementById('errorMessage').innerHTML = "Failed to create product. " + data.message;
        }
    })
    .catch(error => {
        document.getElementById('errorMessage').innerHTML = "Error: " + error.message;
    });
}
</script>

<!-- Include footer -->
<?php include("../includes/footer.php"); ?>
