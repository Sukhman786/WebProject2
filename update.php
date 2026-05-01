<?php
$conn = oci_connect('SYSTEM', '01042006', '127.0.0.1/freepdb1');

$search_id = $_GET['booking_id'] ?? '';
$row = null;

if ($search_id) {
    $sql = "SELECT p.RAW_ID, p.TRACKING_ID, 
                   s.SENDER_NAME, s.SENDER_MOBILE, s.SENDER_ADDRESS, 
                   r.RECEIVER_NAME, r.RECEIVER_MOBILE, r.RECEIVER_ADDRESS, 
                   p.PARCEL_TYPE, p.WEIGHT_GRAMS, p.DELIVERY_TYPE, p.PAYMENT_MODE,
                   TO_CHAR(p.BOOKING_DATE, 'DD-MON-YYYY HH:MI AM') as BOOKING_DATE 
            FROM parcelji p
            JOIN senderji s ON p.sender_id = s.sender_id
            JOIN receiverji r ON p.receiver_id = r.receiver_id
            WHERE p.TRACKING_ID = :bid";
            
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
    <title>Update Booking - Courier System</title>
    <link rel="stylesheet" href="search.css"> <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .details-section{
            display:flex;
            flex-direction: column;
        }

        .details-container{
            margin-bottom: 25px;
        }

        .update-card {
            background: #111;
            border: 1px solid #333;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
        }
        .update-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(57, 255, 20, 0.1);
        }
        .old-value-box {
            font-size: 1.2rem;
            color: #888;
            padding: 10px;
            background: rgba(255, 255, 255, 0.02);
            border-left: 3px solid var(--main-color);
        }
        .label-tag {
            color: var(--main-color);
            font-size: 1rem;
            text-transform: uppercase;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }
        .section-title {
            color: #fff;
            font-size: 1.8rem;
            margin: 20px 0 15px;
            border-bottom: 2px solid var(--main-color);
            display: inline-block;
        }
    </style>
</head>

<body>
    <section class="details-section">
        <div class="details-container">
            <h1 class="h1-contact">Update <span class="highlight">Booking</span></h1>

            <div class="search-box-container" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 30px;">
                <form action="update.php" method="GET" style="display: flex; align-items: center; margin: 0;">
                    <input type="text" name="booking_id" placeholder="Enter Tracking ID (e.g. S001)" 
                        class="input-form" style="margin: 0; width: 250px;" 
                        value="<?php echo htmlspecialchars($search_id); ?>" required>
                    <button type="submit" class="input-btn" style="margin-left: 10px;">FETCH</button>
                </form>
            </div>

            <?php if ($row): ?>
            <form id="fullUpdateForm" class="update-card">
                <input type="hidden" name="raw_id" value="<?php echo $row['RAW_ID']; ?>">
                
                <h2 class="section-title">1. Sender Information</h2>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Name</span><?php echo $row['SENDER_NAME']; ?></div>
                    <input name="s_name" class="input-form" type="text" value="<?php echo $row['SENDER_NAME']; ?>" required>
                </div>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Mobile</span><?php echo $row['SENDER_MOBILE']; ?></div>
                    <input name="s_mobile" class="input-form" type="tel" pattern="[0-9]{10}" value="<?php echo $row['SENDER_MOBILE']; ?>" required>
                </div>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Address</span><?php echo $row['SENDER_ADDRESS']; ?></div>
                    <input name="s_address" class="input-form" type="text" value="<?php echo $row['SENDER_ADDRESS']; ?>" required>
                </div>

                <h2 class="section-title">2. Receiver Information</h2>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Name</span><?php echo $row['RECEIVER_NAME']; ?></div>
                    <input name="r_name" class="input-form" type="text" value="<?php echo $row['RECEIVER_NAME']; ?>" required>
                </div>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Mobile</span><?php echo $row['RECEIVER_MOBILE']; ?></div>
                    <input name="r_mobile" class="input-form" type="tel" pattern="[0-9]{10}" value="<?php echo $row['RECEIVER_MOBILE']; ?>" required>
                </div>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Address</span><?php echo $row['RECEIVER_ADDRESS']; ?></div>
                    <input name="r_address" class="input-form" type="text" value="<?php echo $row['RECEIVER_ADDRESS']; ?>" required>
                </div>

                <h2 class="section-title">3. Parcel & Payment</h2>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Type (<?php echo $row['PARCEL_TYPE']; ?>)</span>Select New Option:</div>
                    <select name="p_type" class="input-form">
                        <option value="" disabled selected>Select Parcel Type</option>
                        <option value="D" <?php if($row['PARCEL_TYPE']=='D') echo 'selected'; ?>>Documents</option>
                        <option value="E" <?php if($row['PARCEL_TYPE']=='E') echo 'selected'; ?>>Electronics</option>
                        <option value="C" <?php if($row['PARCEL_TYPE']=='C') echo 'selected'; ?>>Clothes</option>
                        <option value="F" <?php if($row['PARCEL_TYPE']=='F') echo 'selected'; ?>>Fragile</option>
                    </select>
                </div>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Weight (g)</span>Add New Value:</div>
                    <input name="weight" class="input-form" type="number" step="0.1" value="<?php echo $row['WEIGHT_GRAMS']; ?>" required>
                </div>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Delivery (<?php echo $row['DELIVERY_TYPE']; ?>)</span>Select New Option:</div>
                    <select name="d_type" class="input-form">
                        <option value="N" <?php if($row['DELIVERY_TYPE']=='N') echo 'selected'; ?>>Normal</option>
                        <option value="E" <?php if($row['DELIVERY_TYPE']=='E') echo 'selected'; ?>>Express</option>
                        <option value="S" <?php if($row['DELIVERY_TYPE']=='S') echo 'selected'; ?>>Same-day</option>
                    </select>
                </div>
                <div class="update-grid">
                    <div class="old-value-box"><span class="label-tag">Payment (<?php echo $row['PAYMENT_MODE']; ?>)</span>Select New Option:</div>
                    <select name="p_mode" class="input-form">
                        <option value="C" <?php if($row['PAYMENT_MODE']=='C') echo 'selected'; ?>>COD</option>
                        <option value="O" <?php if($row['PAYMENT_MODE']=='O') echo 'selected'; ?>>Online</option>
                        <option value="P" <?php if($row['PAYMENT_MODE']=='P') echo 'selected'; ?>>Prepaid</option>
                    </select>
                </div>

                <div style="display: flex; gap: 20px; margin-top: 30px;">
                    <button id="execUpdate" class="input-btn" type="button">Update Booking</button>
                </div>
            </form>

            <?php elseif ($search_id): ?>
                <h2 style="text-align:center; color:#fff; margin-top: 20px;">Booking ID: <?php echo htmlspecialchars($search_id); ?> Not Found!</h2>
            <?php endif; ?>

        </div>

        <div class="action-btns">
            <a href="index.html"><button class="input-btn secondary-btn">Back to Home</button></a>
        </div>

    </section>

    <script>
        document.getElementById('execUpdate').addEventListener('click', function() {
            fetch('update_process.php', { method: 'POST', body: new FormData(document.getElementById('fullUpdateForm')) })
            .then(res => res.text())
            .then(data => { alert(data); if(data.includes("Successful")) window.location.href = 'displayAll.php'; });
        });
    </script>
</body>
</html>