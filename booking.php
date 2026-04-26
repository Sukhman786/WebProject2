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

    $trackingId = "";

    $sql = "INSERT INTO courier_bookings (
                SENDER_NAME, SENDER_MOBILE, SENDER_ADDRESS, 
                RECEIVER_NAME, RECEIVER_MOBILE, RECEIVER_ADDRESS, 
                PARCEL_TYPE, WEIGHT_GRAMS, DELIVERY_TYPE, PAYMENT_MODE
            ) VALUES (
                :sn, :sm, :sa, :rn, :rm, :ra, :pt, :wg, :dt, :pm
            ) RETURNING TRACKING_ID INTO :generated_id";

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
    oci_bind_by_name($stmt, ':generated_id', $trackingId, 20, SQLT_CHR);

    if (oci_execute($stmt)) {
        oci_commit($conn);
        echo $trackingId; 
    } else {
        $e = oci_error($stmt);
        echo "DB Error: " . $e['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>