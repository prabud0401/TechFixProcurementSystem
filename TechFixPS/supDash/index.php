<?php
// Start the session
session_start();

// Include header
include("../includes/header.php");

// Initialize variables for error and success messages
$errorMessage = '';
$successMessage = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = $_POST['username']; // Changed to username
    $password = $_POST['password'];

    // Hash the password using MD5 (though it's better to use more secure hashing methods like bcrypt)
    $hashedPassword = $password;

    // Prepare the data to be sent to the API
    $supplierData = array(
        'username' => $username, // Changed to username
        'password' => $hashedPassword
    );

    // Convert the data into JSON format
    $jsonData = json_encode($supplierData);

    // The API URL for logging in
    $apiUrl = "https://localhost:4000/api/supplier/login/";

    // Initialize cURL session
    $ch = curl_init($apiUrl);

    // Set cURL options for the POST request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors during the request
    if (curl_errno($ch)) {
        $errorMessage = "Error: " . curl_error($ch);
    } else {
        // Decode the API response
        $result = json_decode($response, true);

        // Check if the API returned a success message
        if (isset($result['message'])) {
            if (strpos(strtolower($result['message']), 'login successful') !== false) {
                // Store the supplierID in the session
                $_SESSION['supplierID'] = $result['supplierID'];
                
                // Redirect to supplier dashboard
                header("Location: ./SupDash.php");
                exit(); // Always exit after a header redirection to stop further execution
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
?>

<!-- HTML Structure for the supplier Login Form -->
<div class="h-[80vh] bg-gray-900 flex flex-col justify-center items-center">
    <div class="w-full max-w-md p-8 space-y-6 bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-center text-3xl font-extrabold text-white">
            supplier Login
        </h2>

        <!-- Display error message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="text-red-500 text-center mb-4"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form class="space-y-6" action="" method="POST">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" name="username" type="text" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Username">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Log in
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Include footer -->
<?php include("../includes/footer.php"); ?>
