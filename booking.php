<?php
$username = 'SYSTEM';
$password = '01042006'; 
$connection_string = '127.0.0.1/freepdb1';

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

$s_name = $_POST['s_name'] ?? '';
$s_mobile  = $_POST['s_mobile'] ?? '';
$s_address = $_POST['s_address'] ?? '';
$r_name = $_POST['r_name'] ?? '';
$r_mobile  = $_POST['r_mobile'] ?? '';
$r_address = $_POST['r_address'] ?? '';
$p_type = $_POST['p_type'] ?? '';
$weight = $_POST['weight'] ?? '';
$d_type = $_POST['d_type'] ?? '';
$p_mode = $_POST['p_mode'] ?? '';

$sender_id = 0;
$receiver_id = 0;
$trackingId = "";

// INSERT SENDER DETAILS--------------------------------------------------
$checkS = "SELECT sender_id FROM senderji WHERE SENDER_MOBILE = :sm";
$stmtS_check = oci_parse($conn, $checkS);
oci_bind_by_name($stmtS_check, ':sm', $s_mobile);
oci_execute($stmtS_check);
$rowS = oci_fetch_array($stmtS_check, OCI_ASSOC);

if ($rowS) {
    $sender_id = $rowS['SENDER_ID'];
    $res1 = true;
}

else {
    $sql1 = "INSERT INTO senderji (SENDER_NAME, SENDER_MOBILE, SENDER_ADDRESS) 
                VALUES (:sn, :sm, :sa) RETURNING sender_id INTO :sid";
    $stmt1 = oci_parse($conn, $sql1);

    oci_bind_by_name($stmt1, ':sn', $s_name);
    oci_bind_by_name($stmt1, ':sm', $s_mobile);
    oci_bind_by_name($stmt1, ':sa', $s_address);
    oci_bind_by_name($stmt1, ':sid', $sender_id, -1, SQLT_INT);
    
    $res1 = oci_execute($stmt1, OCI_NO_AUTO_COMMIT);
}

// INSERT RECEIVER DETAILS--------------------------------------------------
$checkR = "SELECT receiver_id FROM receiverji WHERE RECEIVER_MOBILE = :rm";
$stmtR_check = oci_parse($conn, $checkR);

oci_bind_by_name($stmtR_check, ':rm', $r_mobile);
oci_execute($stmtR_check);

$rowR = oci_fetch_array($stmtR_check, OCI_ASSOC);

if ($rowR) {
    $receiver_id = $rowR['RECEIVER_ID'];
    $res2 = true;
}

else {
    $sql2 = "INSERT INTO receiverji (RECEIVER_NAME, RECEIVER_MOBILE, RECEIVER_ADDRESS) 
                VALUES (:rn, :rm, :ra) RETURNING receiver_id INTO :rid";
    $stmt2 = oci_parse($conn, $sql2);

    oci_bind_by_name($stmt2, ':rn', $r_name);
    oci_bind_by_name($stmt2, ':rm', $r_mobile);
    oci_bind_by_name($stmt2, ':ra', $r_address);
    oci_bind_by_name($stmt2, ':rid', $receiver_id, -1, SQLT_INT);

    $res2 = oci_execute($stmt2, OCI_NO_AUTO_COMMIT);
}

// INSERT PARCEL DETAILS-------------------------------------------------------
$sql3 = "INSERT INTO parcelji (sender_id, receiver_id, PARCEL_TYPE, WEIGHT_GRAMS, DELIVERY_TYPE, PAYMENT_MODE) 
            VALUES (:sid, :rid, :pt, :wg, :dt, :pm) RETURNING TRACKING_ID INTO :tid";
$stmt3 = oci_parse($conn, $sql3);

oci_bind_by_name($stmt3, ':sid', $sender_id);
oci_bind_by_name($stmt3, ':rid', $receiver_id);
oci_bind_by_name($stmt3, ':pt', $p_type);
oci_bind_by_name($stmt3, ':wg', $weight);
oci_bind_by_name($stmt3, ':dt', $d_type);
oci_bind_by_name($stmt3, ':pm', $p_mode);
oci_bind_by_name($stmt3, ':tid', $trackingId, 20, SQLT_CHR);

$res3 = oci_execute($stmt3, OCI_NO_AUTO_COMMIT);

if ($res1 && $res2 && $res3) {
    oci_commit($conn); 
    echo $trackingId; 
}

else {
    oci_rollback($conn); 
    echo "Error in Booking Process.";
}

oci_close($conn);
}
?>