<?php
// Start the session
session_start();

// Include header
include("../includes/header.php");

// API URL to fetch customer data
$apiUrl = "https://localhost:4000/api/customer/";

// Initialize variables to store customer details and messages
$customers = [];
$errorMessage = '';
$successMessage = '';

// Fetch all customer data
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

    // Check if the API returned customer data
    if (is_array($result)) {
        $customers = $result;
    } else {
        $errorMessage = "Failed to fetch customer details.";
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
                    <th class="px-6 py-3">Customer ID</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Address</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr data-customer-id="<?php echo $customer['customerID']; ?>" data-password="<?php echo $customer['password']; ?>">
                    <td class="px-6 py-4"><?php echo $customer['customerID']; ?></td>
                    <td class="px-6 py-4">
                        <input type="text" name="name" value="<?php echo $customer['name']; ?>" class="bg-gray-700 text-white px-3 py-2 rounded">
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="email" value="<?php echo $customer['email']; ?>" class="bg-gray-700 text-white px-3 py-2 rounded" readonly>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="phone" value="<?php echo $customer['phone']; ?>" class="bg-gray-700 text-white px-3 py-2 rounded">
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="address" value="<?php echo $customer['address']; ?>" class="bg-gray-700 text-white px-3 py-2 rounded">
                    </td>
                    <td class="px-6 py-4 flex space-x-2">
                        <!-- Save Button -->
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded" onclick="saveCustomer(<?php echo $customer['customerID']; ?>)">Save</button>
                        <!-- Delete Button -->
                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded" onclick="deleteCustomer(<?php echo $customer['customerID']; ?>)">Delete</button>
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
// Save Customer Function
function saveCustomer(customerID) {
    // Get the input values from the row where customerID matches
    var row = document.querySelector(`tr[data-customer-id='${customerID}']`);
    var name = row.querySelector("input[name='name']").value;
    var email = row.querySelector("input[name='email']").value;
    var phone = row.querySelector("input[name='phone']").value;
    var address = row.querySelector("input[name='address']").value;
    var password = row.getAttribute("data-password"); // Get password from data attribute

    // Send POST request to saveCus.php via AJAX
    $.ajax({
        url: 'saveCus.php',
        type: 'POST',
        data: {
            customerID: customerID,
            name: name,
            email: email,
            phone: phone,
            address: address,
            password: password // Use stored password
        },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.status === 'success' && result.message === "Customer updated successfully!") {
                alert(result.message);
                location.reload(); // Reload the page to reflect the saved changes
            } else {
                alert(result.message);
            }
        },
        error: function(error) {
            console.error('Error:', error);
            alert('An error occurred while saving the customer details.');
        }
    });
}

// Delete Customer Function
function deleteCustomer(customerID) {
    if (confirm("Are you sure you want to delete this customer?")) {
        // Send DELETE request to deleteCus.php via AJAX
        $.ajax({
            url: 'deleteCus.php',
            type: 'POST',
            data: { customerID: customerID },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success' && result.message === "Customer deleted successfully!") {
                    alert(result.message);
                    location.reload(); // Reload the page to update the customer list
                } else {
                    alert(result.message);
                }
            },
            error: function(error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the customer.');
            }
        });
    }
}
</script>
