<?php
// Start the session
session_start();

// Check if customerID is set in the session, if not redirect to login
if (!isset($_SESSION['customerID'])) {
    header("Location: ../log/index.php");
    exit();
}

// Get the customerID from the session
$customerID = $_SESSION['customerID'];

// API URL to fetch customer data
$apiUrl = "https://localhost:4000/api/customer/$customerID";

// Initialize variables to store customer details and messages
$name = $email = $phone = $address = $password = $errorMessage = $successMessage = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Assume the password hasn't changed, so use the existing one from the API response
    $password = $_POST['password']; // Hidden input storing the unchanged password

    // Prepare the data to be sent in the PUT request
    $customerData = array(
        'customerID' => $customerID,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'password' => $password // Use the existing password
    );

    // Convert the data into JSON format
    $jsonData = json_encode($customerData);

    // Initialize cURL session for PUT request
    $ch = curl_init($apiUrl);

    // Set cURL options for PUT request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors in the request
    if (curl_errno($ch)) {
        $errorMessage = "Error: " . curl_error($ch);
    } else {
        // Decode the API response
        $result = json_decode($response, true);

        // Check if the API returned a success or error message
        if (isset($result['message'])) {
            if (strpos(strtolower($result['message']), 'successfully') !== false) {
                $successMessage = htmlspecialchars($result['message']);
            } else {
                $errorMessage = htmlspecialchars($result['message']);
            }
        } else {
            $errorMessage = "Invalid response from the server. Please try again.";
        }
    }

    // Close cURL session
    curl_close($ch);
} else {
    // Fetch current customer data if the form is not submitted yet
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    if (!curl_errno($ch)) {
        $result = json_decode($response, true);
        if (isset($result['customerID'])) {
            $name = htmlspecialchars($result['name']);
            $email = htmlspecialchars($result['email']);
            $phone = htmlspecialchars($result['phone']);
            $address = htmlspecialchars($result['address']);
            $password = htmlspecialchars($result['password']); // Corrected this assignment
        } else {
            $errorMessage = "Failed to fetch customer details.";
        }
    }

    curl_close($ch);
}

// Include header
include("../includes/header.php");
?>
<section class="w-full h-screen flex">
    <div class="w-96 h-full">
        <?php include("./nav.php"); ?>
    </div>
    <div class="w-full h-full flex flex-col justify-between pt-16">
        <div class="bg-gray-900 flex flex-col justify-center items-center text-black">
            <!-- Display error or success message -->
            <?php if (!empty($errorMessage)): ?>
                <div class="text-red-500 text-center mb-4"><?php echo $errorMessage; ?></div>
            <?php elseif (!empty($successMessage)): ?>
                <div class="text-green-500 text-center mb-4"><?php echo $successMessage; ?></div>
            <?php endif; ?>

            <!-- Account Management Form -->
            <form class="w-full max-w-md p-8 bg-gray-800 rounded-lg shadow-lg space-y-6" action="" method="POST">
                <h2 class="text-center text-3xl font-extrabold text-white mb-4">Manage Account</h2>

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-white">Name</label>
                    <input id="name" name="name" type="text" value="<?php echo $name; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white">Email</label>
                    <input id="email" name="email" type="email" value="<?php echo $email; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" readonly>
                </div>

                <!-- Phone Field -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-white">Phone</label>
                    <input id="phone" name="phone" type="text" value="<?php echo $phone; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Address Field -->
                <div>
                    <label for="address" class="block text-sm font-medium text-white">Address</label>
                    <input id="address" name="address" type="text" value="<?php echo $address; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Hidden Password Field to send unchanged password -->
                <input type="hidden" name="password" value="<?php echo $password; ?>">

                <!-- Submit and Delete Buttons -->
                <div class="flex w-full space-x-4">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Details
                    </button>
                    <button type="button" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="deleteCustomer('<?php echo $customerID; ?>')">Delete</button>
                </div>
            </form>
        </div>
        <!-- Include footer -->
        <?php include("../includes/footer.php"); ?>
    </div>

</section>
    




<script>
// Delete Customer Function
function deleteCustomer(customerID) {
    if (confirm("Are you sure you want to delete this customer?")) {
        // Send DELETE request to deleteCus.php via AJAX
        $.ajax({
            url: '../mangDash/deleteCus.php',
            type: 'POST',
            data: { customerID: customerID },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success' && result.message === "Customer deleted successfully!") {
                    alert(result.message);
                    window.location.href = '../'; // Redirect to the parent directory after successful deletion
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
