<?php
$conn = oci_connect('SYSTEM', '01042006', '127.0.0.1/freepdb1');

$delete_msg = "";

if (isset($_POST['delete_id'])) {
    $del_id = $_POST['delete_id'];

    $del_sql = "DELETE FROM parcelji WHERE RAW_ID = :bid";
    $del_stmt = oci_parse($conn, $del_sql);
    oci_bind_by_name($del_stmt, ':bid', $del_id);
    
    if (oci_execute($del_stmt)) {
        oci_commit($conn);
        $delete_msg = "Record deleted successfully.";
    }
    
    else {
        $e = oci_error($del_stmt);
        $delete_msg = "Error: " . $e['message'];
    }
}

$sql = "SELECT p.RAW_ID, p.TRACKING_ID, s.SENDER_NAME, s.SENDER_MOBILE, s.SENDER_ADDRESS, 
               r.RECEIVER_NAME, r.RECEIVER_MOBILE, r.RECEIVER_ADDRESS, 
               p.PARCEL_TYPE, p.WEIGHT_GRAMS, p.DELIVERY_TYPE, p.PAYMENT_MODE,
               TO_CHAR(p.BOOKING_DATE, 'DD-MON-YYYY HH:MI AM') as BOOKING_DATE 
        FROM parcelji p
        JOIN senderji s ON p.sender_id = s.sender_id
        JOIN receiverji r ON p.receiver_id = r.receiver_id
        ORDER BY p.RAW_ID DESC";

$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings - Courier System</title>
    <link rel="stylesheet" href="displayAll.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <?php if ($delete_msg): ?>
    <script>
        window.onload = function() {
            setTimeout(function() {
                alert("<?php echo $delete_msg; ?>");
                window.location.href = 'displayAll.php';
            }, 100); 
        };
    </script>
    <?php endif; ?>

    <section class="details-section">
        <div class="details-container">
            <h1 class="h1-contact">All <span class="highlight">Bookings</span></h1>

            <div class="cards-wrapper">
                <?php 
                $count = 0;
                while ($row = oci_fetch_array($stmt, OCI_ASSOC)): 
                    $count++;
                ?>
                <div class="receipt-card">
                    <div class="receipt-header">
                        <div>
                            <p><strong>Tracking ID:</strong> <span class="highlight"><?php echo $row['TRACKING_ID']; ?></span></p>
                            <p><strong>Date:</strong> <span class="highlight"><?php echo $row['BOOKING_DATE']; ?></span></p>
                        </div>
                        
                        <form action="displayAll.php" method="POST" onsubmit="return confirm('Delete this record permanently?')">
                            <input type="hidden" name="delete_id" value="<?php echo $row['RAW_ID']; ?>">
                            <button type="submit" class="input-btn delete-btn" style="padding: 5px 15px; font-size: 1.1rem;">DELETE</button>
                        </form>
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
                <?php endwhile; ?>

                <?php if ($count == 0): ?>
                    <h2 style="text-align:center; color:white; margin-top: 20px;">No records found.</h2>
                <?php endif; ?>
            </div>

            <div class="action-btns" style="margin-top: 40px;">
                <a href="index.html"><button class="input-btn secondary-btn">Back to Home</button></a>
            </div>
        </div>
    </section>
</body>
</html>