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

$get_ids_sql = "SELECT sender_id, receiver_id FROM parcelji WHERE RAW_ID = :rid";
$get_stmt = oci_parse($conn, $get_ids_sql);
oci_bind_by_name($get_stmt, ':rid', $id);
oci_execute($get_stmt);
$ids = oci_fetch_array($get_stmt, OCI_ASSOC);

if ($ids) {
    $sid = $ids['SENDER_ID'];
    $rid = $ids['RECEIVER_ID'];

    $sql1 = "UPDATE senderji SET SENDER_NAME = :sn, SENDER_MOBILE = :sm, SENDER_ADDRESS = :sa WHERE sender_id = :sid";
    $stmt1 = oci_parse($conn, $sql1);
    oci_bind_by_name($stmt1, ':sn', $s_name);
    oci_bind_by_name($stmt1, ':sm', $s_mobile);
    oci_bind_by_name($stmt1, ':sa', $s_address);
    oci_bind_by_name($stmt1, ':sid', $sid);

    oci_execute($stmt1, OCI_NO_AUTO_COMMIT);

    $sql2 = "UPDATE receiverji SET RECEIVER_NAME = :rn, RECEIVER_MOBILE = :rm, RECEIVER_ADDRESS = :ra WHERE receiver_id = :rid";
    $stmt2 = oci_parse($conn, $sql2);
    oci_bind_by_name($stmt2, ':rn', $r_name);
    oci_bind_by_name($stmt2, ':rm', $r_mobile);
    oci_bind_by_name($stmt2, ':ra', $r_address);
    oci_bind_by_name($stmt2, ':rid', $rid);

    oci_execute($stmt2, OCI_NO_AUTO_COMMIT);

    $sql3 = "UPDATE parcelji SET PARCEL_TYPE = :pt, WEIGHT_GRAMS = :wg, DELIVERY_TYPE = :dt, PAYMENT_MODE = :pm WHERE RAW_ID = :rid";
    $stmt3 = oci_parse($conn, $sql3);
    oci_bind_by_name($stmt3, ':pt', $p_type);
    oci_bind_by_name($stmt3, ':wg', $weight);
    oci_bind_by_name($stmt3, ':dt', $d_type);
    oci_bind_by_name($stmt3, ':pm', $p_mode);
    oci_bind_by_name($stmt3, ':rid', $id);
    
    if (oci_execute($stmt3, OCI_NO_AUTO_COMMIT)) {
        oci_commit($conn);
        echo "Update Successful! The record has been synced with the database.";
    } else {
        oci_rollback($conn);
        $e = oci_error($stmt3);
        echo "Database Error: " . $e['message'];
    }
}

oci_close($conn);
?>