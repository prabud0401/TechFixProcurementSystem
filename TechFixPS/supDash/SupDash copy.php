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
$name = $email = $phone = $address = $errorMessage = '';

// Fetch current supplier data
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
    if (isset($result['supplierID'])) {
        $name = htmlspecialchars($result['name']);
    } else {
        $errorMessage = "Failed to fetch supplier details.";
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
        <!-- supplier Details Display -->
        <h2 class="text-center text-2xl font-bold text-gray-500">Welcome, <span class="text-3xl font-extrabold text-white"><?php echo $name; ?></span> </h2>
    <?php endif; ?>

    
</div>

<!-- Include footer -->
<?php include("../includes/footer.php"); ?>
