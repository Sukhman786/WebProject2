<?php
// 1. Connection Details
$username = 'SYSTEM'; // Use the user you created if you changed it
$password = '01042006'; 
$connection_string = '127.0.0.1/freepdb1'; // 'FREE' is the default for Oracle 26ai

$conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

// 2. Get data from POST
$s_name = $_POST['s_name'] ?? '';
$s_mob  = $_POST['s_mobile'] ?? '';
$s_addr = $_POST['s_address'] ?? '';
$r_name = $_POST['r_name'] ?? '';
$r_mob  = $_POST['r_mobile'] ?? '';
$r_addr = $_POST['r_address'] ?? '';
$p_type = $_POST['p_type'] ?? '';
$weight = $_POST['weight'] ?? '';
$d_type = $_POST['d_type'] ?? '';
$p_mode = $_POST['p_mode'] ?? '';
// ... collect receiver details and types similarly ...

$random_num = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT); 
$custom_id = "S" . $random_num; // Result example: S001, S085, S999

// 3. Prepare SQL with auto-generating ID
$sql = "INSERT INTO courier_bookings (
            booking_id, sender_name, sender_mobile, sender_address, 
            receiver_name, receiver_mobile, receiver_address,
            parcel_type, weight_grams, delivery_type, payment_mode
        ) VALUES (
            :id, :sn, :sm, :sa, :rn, :rm, :ra, :pt, :wg, :dt, :pm
        )";

$stmt = oci_parse($conn, $sql);

// 4. Bind variables (prevents SQL injection)
oci_bind_by_name($stmt, ':sn', $s_name);
oci_bind_by_name($stmt, ':sm', $s_mob);
oci_bind_by_name($stmt, ':sa', $s_addr);
oci_bind_by_name($stmt, ':rn', $r_name);
oci_bind_by_name($stmt, ':rm', $r_mob);
oci_bind_by_name($stmt, ':ra', $r_addr);
oci_bind_by_name($stmt, ':pt', $p_type);
oci_bind_by_name($stmt, ':wg', $weight);
oci_bind_by_name($stmt, ':dt', $d_type);
oci_bind_by_name($stmt, ':pm', $p_mode);
oci_bind_by_name($stmt, ':id', $custom_id, 32);

// 5. Execute
$result = oci_execute($stmt);

if ($result) {
    echo $custom_id;
} else {
    $e = oci_error($stmt);
    echo "Error: " . $e['message'];
}

oci_free_statement($stmt);
oci_close($conn);
?>