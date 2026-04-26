<?php
// 1. Connection Details
$conn = oci_connect('SYSTEM', '01042006', '127.0.0.1/freepdb1');

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

// 2. Updated Delete Request Logic
if (isset($_POST['delete_id'])) {
    $del_id = $_POST['delete_id'];
    // Use RAW_ID for the actual deletion query
    $del_sql = "DELETE FROM courier_bookings WHERE RAW_ID = :bid";
    $del_stmt = oci_parse($conn, $del_sql);
    oci_bind_by_name($del_stmt, ':bid', $del_id);
    
    if (oci_execute($del_stmt)) {
        echo "<script>alert('Booking has been permanently deleted from the Database.'); window.location.href='search.php';</script>";
    } else {
        $e = oci_error($del_stmt);
        echo "<script>alert('Error deleting record: " . $e['message'] . "');</script>";
    }
}

$search_id = $_GET['booking_id'] ?? '';
$row = null;

if ($search_id) {
    $sql = "SELECT RAW_ID, TRACKING_ID, SENDER_NAME, SENDER_MOBILE, SENDER_ADDRESS, 
               RECEIVER_NAME, RECEIVER_MOBILE, RECEIVER_ADDRESS, 
               PARCEL_TYPE, WEIGHT_GRAMS, DELIVERY_TYPE, PAYMENT_MODE,
               TO_CHAR(BOOKING_DATE, 'DD-MON-YYYY HH:MI AM') as BOOKING_DATE 
        FROM courier_bookings 
        WHERE TRACKING_ID = :bid";
        
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':bid', $search_id);
    oci_execute($stmt);
    
    $row = oci_fetch_array($stmt, OCI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - Courier System</title>
    <link rel="stylesheet" href="search.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <section class="details-section">
        <div class="details-container">
            <h1 class="h1-contact">Booking <span class="highlight">Summary</span></h1>

            <div class="search-box-container" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 30px;">
    
                <form action="search.php" method="GET" style="display: flex; align-items: center; margin: 0;">
                    <input type="text" name="booking_id" placeholder="Enter ID (e.g. S001)" 
                        class="input-form" style="margin: 0; width: 250px;" 
                        value="<?php echo htmlspecialchars($search_id); ?>" required>
                    
                    <button type="submit" class="input-btn" style="margin-left: 10px;">SEARCH</button>
                </form>

                <?php if ($row): ?>
                <form action="search.php" method="POST" onsubmit="return confirm('Do you want to Delete this record?')" style="display: flex; align-items: center; margin: 0;">
                    <input type="hidden" name="delete_id" value="<?php echo $row['RAW_ID']; ?>">
                    <button type="submit" class="input-btn delete-btn">DELETE</button>
                </form>
                <?php endif; ?>

            </div>
            
            <?php if ($row): ?>
            <div class="receipt-card">
                <div class="receipt-header">
                    <p><strong>Tracking ID:</strong> <span class="highlight"><?php echo $row['TRACKING_ID']; ?></span></p>
                    <p><strong>Date:</strong> <span class="highlight"><?php echo $row['BOOKING_DATE']; ?></span></p>
                </div>

                <div class="receipt-grid">
                    <div class="info-group">
                        <h3><i class='bx bxs-user-pin'></i> Sender Details</h3>
                        <p><strong>Name:</strong> <?php echo $row['SENDER_NAME']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $row['SENDER_MOBILE']; ?></p>
                        <p><strong>Address:</strong> <?php echo $row['SENDER_ADDRESS']; ?></p>
                    </div>

                    <div class="info-group">
                        <h3><i class='bx bxs-map-pin'></i> Receiver Details</h3>
                        <p><strong>Name:</strong> <?php echo $row['RECEIVER_NAME']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $row['RECEIVER_MOBILE']; ?></p>
                        <p><strong>Address:</strong> <?php echo $row['RECEIVER_ADDRESS']; ?></p>
                    </div>
                </div>

                <hr class="divider">

                <div class="parcel-info">
                    <h3><i class='bx bxs-package'></i> Parcel Information</h3>
                    <div class="specs">
                        <span><strong>Type:</strong> <?php echo $row['PARCEL_TYPE']; ?></span>
                        <span><strong>Weight:</strong> <?php echo $row['WEIGHT_GRAMS']; ?>g</span>
                        <span><strong>Delivery:</strong> <?php echo $row['DELIVERY_TYPE']; ?></span>
                    </div>
                </div>
            </div>

            <?php elseif ($search_id): ?>
                <h2 style="text-align:center; color:white; margin-bottom: 20px;">ID: <?php echo htmlspecialchars($search_id); ?> Not Found!</h2>
            <?php endif; ?>

            <div class="action-btns">
                <a href="index.html"><button class="input-btn secondary-btn">Back to Home</button></a>
            </div>
        </div>
    </section>
</body>
</html>