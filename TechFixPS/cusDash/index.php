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
$name = $email = $phone = $address = $errorMessage = '';

// Fetch current customer data
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
    if (isset($result['customerID'])) {
        $name = htmlspecialchars($result['name']);
    } else {
        $errorMessage = "Failed to fetch customer details.";
    }
}

// Close cURL session
curl_close($ch);

// Include header
include("../includes/header.php");
?>
<!-- Navigation -->
<div class="flex w-full space-x-8 justify-center bg-black">    
        <?php include("./nv.php"); ?>
    </div>
<!-- Dashboard HTML Structure -->
<div class="h-[80vh] bg-gray-900 flex flex-col justify-around items-center">
    
    <!-- Display error or success message -->
    <?php if (!empty($errorMessage)): ?>
        <div class="text-red-500 text-center mb-4"><?php echo $errorMessage; ?></div>
    <?php else: ?>
        <!-- Customer Details Display -->
        <h2 class="text-center text-2xl font-bold text-gray-500">Welcome, <span class="text-3xl font-extrabold text-white"><?php echo $name; ?></span> </h2>
    <?php endif; ?>

    
</div>

<!-- Include footer -->
<?php include("../includes/footer.php"); ?>
