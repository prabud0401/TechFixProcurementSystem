<?php
// Start the session
session_start();

// Check if supplierID is set in the session, if not redirect to login
if (!isset($_SESSION['supplierID'])) {
    header("Location: ./index.php");
    exit();
}

// Get the supplierID from the session
$supplierID = $_SESSION['supplierID'];

// API URL to fetch supplier data
$apiUrl = "https://localhost:4000/api/supplier/$supplierID";

// Initialize variables to store supplier details and messages
$name = $email = $phone = $address = $username = $oldPassword = $errorMessage = $successMessage = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $username = $_POST['username']; // Hidden, but needed for the API request
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Fetch the current supplier data to compare old password
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!curl_errno($ch)) {
        $result = json_decode($response, true);

        if (isset($result['password'])) {
            // Verify old password
            if ($oldPassword !== $result['password']) {
                $errorMessage = "Old password is incorrect.";
            } elseif ($newPassword !== $confirmPassword) {
                $errorMessage = "New passwords do not match.";
            } else {
                // Hash the new password
                $hashedNewPassword = $newPassword;

                // Prepare the data to be sent in the PUT request
                $supplierData = array(
                    'supplierID' => $supplierID,
                    'name' => $name,
                    'username' => $username,
                    'phone' => $phone,
                    'address' => $address,
                    'password' => $hashedNewPassword // Use the new password
                );

                // Convert the data into JSON format
                $jsonData = json_encode($supplierData);

                // Initialize cURL session for PUT request
                $ch = curl_init($apiUrl);
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
                        if (strpos(strtolower($result['message']), 'supplier updated successfully!') !== false) {
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
            }
        }
    }
} else {
    // Fetch current supplier data if the form is not submitted yet
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    if (!curl_errno($ch)) {
        $result = json_decode($response, true);
        if (isset($result['supplierID'])) {
            $name = htmlspecialchars($result['name']);
            $username = htmlspecialchars($result['username']);
            $phone = htmlspecialchars($result['phone']);
            $address = htmlspecialchars($result['address']);
        } else {
            $errorMessage = "Failed to fetch supplier details.";
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

                <!-- Username Field (Non-editable) -->
                <div>
                    <label for="username" class="block text-sm font-medium text-white">Username</label>
                    <input id="username" name="username" type="text" value="<?php echo $username; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" readonly>
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

                <!-- Password Fields -->
                <div>
                    <label for="oldPassword" class="block text-sm font-medium text-white">Old Password</label>
                    <input id="oldPassword" name="oldPassword" type="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div>
                    <label for="newPassword" class="block text-sm font-medium text-white">New Password</label>
                    <input id="newPassword" name="newPassword" type="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div>
                    <label for="confirmPassword" class="block text-sm font-medium text-white">Confirm New Password</label>
                    <input id="confirmPassword" name="confirmPassword" type="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
