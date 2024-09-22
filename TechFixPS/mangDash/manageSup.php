<?php
// Start the session
session_start();

// Include header
include("../includes/header.php");

// API URL to fetch supplier data
$apiUrl = "https://localhost:4000/api/supplier/";

// Initialize variables to store supplier details and messages
$suppliers = [];
$errorMessage = '';
$successMessage = '';

// Fetch all supplier data
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
$response = curl_exec($ch);

// Check for errors in the request
if (curl_errno($ch)) {
    $errorMessage = "Error: " . curl_error($ch);
} else {
    // Decode the API response
    $result = json_decode($response, true);

    // Check if the API returned supplier data
    if (is_array($result)) {
        $suppliers = $result;
    } else {
        $errorMessage = "Failed to fetch supplier details.";
    }
}
// Close cURL session
curl_close($ch);
?>

<section class="w-full h-screen flex">
    <div class="w-96 h-full">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between pt-16">

    <div class="bg-gray-900 flex flex-col justify-center items-center text-black py-8 mx-16 h-full">
        <table class="min-w-full bg-gray-800 text-white h-full overflow-auto ">
            <thead>
                <tr>
                    <th class="px-6 py-3">supplier ID</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Address</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($suppliers as $supplier): ?>
                <tr class="" data-supplier-id="<?php echo $supplier['supplierID']; ?>" data-password="<?php echo $supplier['password']; ?>">
                    <td class="px-6 py-4"><?php echo $supplier['supplierID']; ?></td>
                    <td class="px-6 py-4">
                        <input type="text" name="name" value="<?php echo $supplier['name']; ?>" class="bg-gray-700 text-white px-3 py-2 rounded">
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="email" value="<?php echo $supplier['email']; ?>" class="bg-gray-700 text-white px-3 py-2 rounded" readonly>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="phone" value="<?php echo $supplier['phone']; ?>" class="bg-gray-700 text-white px-3 py-2 rounded">
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="address" value="<?php echo $supplier['address']; ?>" class="bg-gray-700 text-white px-3 py-2 rounded">
                    </td>
                    <td class="px-6 py-4 flex space-x-2">
                        <!-- Save Button -->
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded" onclick="savesupplier(<?php echo $supplier['supplierID']; ?>)">Save</button>
                        <!-- Delete Button -->
                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded" onclick="deletesupplier(<?php echo $supplier['supplierID']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<!-- Include footer -->
        <?php include("../includes/footer.php"); ?>
    </div>

</section>

<script>
// Save supplier Function
function savesupplier(supplierID) {
    // Get the input values from the row where supplierID matches
    var row = document.querySelector(`tr[data-supplier-id='${supplierID}']`);
    var name = row.querySelector("input[name='name']").value;
    var email = row.querySelector("input[name='email']").value;
    var phone = row.querySelector("input[name='phone']").value;
    var address = row.querySelector("input[name='address']").value;
    var password = row.getAttribute("data-password"); // Get password from data attribute

    // Send POST request to saveSup.php via AJAX
    $.ajax({
        url: 'saveSup.php',
        type: 'POST',
        data: {
            supplierID: supplierID,
            name: name,
            email: email,
            phone: phone,
            address: address,
            password: password // Use stored password
        },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.status === 'success' && result.message === "supplier updated successfully!") {
                alert(result.message);
                location.reload(); // Reload the page to reflect the saved changes
            } else {
                alert(result.message);
            }
        },
        error: function(error) {
            console.error('Error:', error);
            alert('An error occurred while saving the supplier details.');
        }
    });
}

// Delete supplier Function
function deletesupplier(supplierID) {
    if (confirm("Are you sure you want to delete this supplier?")) {
        // Send DELETE request to deleteSup.php via AJAX
        $.ajax({
            url: 'deleteSup.php',
            type: 'POST',
            data: { supplierID: supplierID },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success' && result.message === "supplier deleted successfully!") {
                    alert(result.message);
                    location.reload(); // Reload the page to update the supplier list
                } else {
                    alert(result.message);
                }
            },
            error: function(error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the supplier.');
            }
        });
    }
}
</script>
