<?php
$conn = oci_connect('SYSTEM', '01042006', '127.0.0.1/freepdb1');

if (!$conn) {
    $e = oci_error();
    exit("Connection failed: " . $e['message']);
}


$id        = $_POST['raw_id'];
$s_name    = $_POST['s_name'];
$s_mobile  = $_POST['s_mobile'];
$s_address = $_POST['s_address'];
$r_name    = $_POST['r_name'];
$r_mobile  = $_POST['r_mobile'];
$r_address = $_POST['r_address'];
$p_type    = $_POST['p_type'];
$weight    = $_POST['weight'];
$d_type    = $_POST['d_type'];
$p_mode    = $_POST['p_mode'];


$sql = "UPDATE courier_bookings SET 
            SENDER_NAME = :sn, 
            SENDER_MOBILE = :sm, 
            SENDER_ADDRESS = :sa, 
            RECEIVER_NAME = :rn, 
            RECEIVER_MOBILE = :rm, 
            RECEIVER_ADDRESS = :ra, 
            PARCEL_TYPE = :pt, 
            WEIGHT_GRAMS = :wg, 
            DELIVERY_TYPE = :dt, 
            PAYMENT_MODE = :pm 
        WHERE RAW_ID = :rid";

$stmt = oci_parse($conn, $sql);

oci_bind_by_name($stmt, ':sn', $s_name);
oci_bind_by_name($stmt, ':sm', $s_mobile);
oci_bind_by_name($stmt, ':sa', $s_address);
oci_bind_by_name($stmt, ':rn', $r_name);
oci_bind_by_name($stmt, ':rm', $r_mobile);
oci_bind_by_name($stmt, ':ra', $r_address);
oci_bind_by_name($stmt, ':pt', $p_type);
oci_bind_by_name($stmt, ':wg', $weight);
oci_bind_by_name($stmt, ':dt', $d_type);
oci_bind_by_name($stmt, ':pm', $p_mode);
oci_bind_by_name($stmt, ':rid', $id);

if (oci_execute($stmt)) {
    echo "Update Successful! The record has been synced with the database.";
} else {
    $e = oci_error($stmt);
    echo "Database Error: " . $e['message'];
}

oci_free_statement($stmt);
oci_close($conn);
?>