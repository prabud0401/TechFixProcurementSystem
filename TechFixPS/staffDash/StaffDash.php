<?php
// Start the session
session_start();

// Check if staffID is set in the session, if not redirect to login
if (!isset($_SESSION['staffID'])) {
    header("Location: ./index.php");
    exit();
}

// Get the staffID from the session
$staffID = $_SESSION['staffID'];

// API URL to fetch staff data
$apiUrl = "https://localhost:4000/api/staff/$staffID";

// Initialize variables to store staff details and messages
$name = $email = $phone = $address = $errorMessage = '';

// Fetch current staff data
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

    // Check if the API returned staff data
    if (isset($result['staffID'])) {
        $name = htmlspecialchars($result['name']);
    } else {
        $errorMessage = "Failed to fetch staff details.";
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
        <!-- staff Details Display -->
        <div>
            <h2 class="text-center text-2xl font-bold text-gray-500">Welcome, <span class="text-3xl font-extrabold text-white"><?php echo $name; ?></span> </h2>
            <p class="text-center text-xl font-bold text-gray-500">staff</p>
         </div>
    <?php endif; ?>

    
</div>

<!-- Include footer -->
<?php include("../includes/footer.php"); ?>
