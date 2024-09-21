<?php
// Include header
include("../includes/header.php");

// Initialize variables for error and success messages
$errorMessage = '';
$successMessage = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    // Hash the password using MD5
    $hashedPassword = md5($password);

    // Prepare the data to be sent to the API
    $customerData = array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'password' => $hashedPassword
    );

    // Convert the data into JSON format
    $jsonData = json_encode($customerData);

    // The API URL for inserting a new customer
    $apiUrl = "https://localhost:4000/api/customer/";

    // Initialize cURL session
    $ch = curl_init($apiUrl);

    // Set cURL options for the POST request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for localhost (not recommended in production)
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

        // Check if the API returned a success message or error
        if (isset($result['message'])) {
            if (strpos(strtolower($result['message']), 'success') !== false) {
                $successMessage = htmlspecialchars($result['message']);
            } else {
                $errorMessage = htmlspecialchars($result['message']);
            }
        } else {
            $errorMessage = "Failed to add customer. Please try again.";
        }
    }

    // Close cURL session
    curl_close($ch);
}
?>

<!-- HTML Structure for the Customer Registration Form -->
<div class="h-[80vh] bg-gray-900 flex flex-col justify-center items-center">
    <div class="w-full max-w-md p-8 space-y-6 bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-center text-3xl font-extrabold text-white">
            Customer Registration
        </h2>

        <!-- Display error or success message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="text-red-500 text-center mb-4"><?php echo $errorMessage; ?></div>
        <?php elseif (!empty($successMessage)): ?>
            <div class="text-green-500 text-center mb-4"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form class="space-y-6" action="" method="POST">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="name" class="sr-only">Name</label>
                    <input id="name" name="name" type="text" autocomplete="name" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Name">
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 sm:text-sm" placeholder="Email address">
                </div>
                <div>
                    <label for="phone" class="sr-only">Phone Number</label>
                    <input id="phone" name="phone" type="tel" autocomplete="phone" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 sm:text-sm" placeholder="Phone Number">
                </div>
                <div>
                    <label for="address" class="sr-only">Address</label>
                    <input id="address" name="address" type="text" autocomplete="address" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 sm:text-sm" placeholder="Address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Password">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Register
                </button>
            </div>
        </form>

        <p class="mt-2 text-center text-sm text-gray-500">
            Already have an account?
            <a href="../log/index.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                Log in
            </a>
        </p>
    </div>
</div>

<!-- Include footer -->
<?php include("../includes/footer.php"); ?>
