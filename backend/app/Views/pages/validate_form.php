<?php

$db = \config\Database::connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$email_id = $_POST['email'] ?? '';
$mobile_no = $_POST['mobile_no'] ?? '';
if (!empty($email_id)) {
    $query = $db->query("SELECT COUNT(*) AS count FROM member_request WHERE email = $email_id");
    $data  = $query->getResultArray();
    $db->close();
    $isValid = true;
    $message = "";
    if (!empty($data)) {
        $isValid = false;
        $message = "Email already exists!";
    }
} else {
    $query = $db->query("SELECT COUNT(*) AS count FROM member_request WHERE mobile_no = $mobile_no");
    $data  = $query->getResultArray();
    $db->close();
    $isValid = true;
    $message = "";
    if (!empty($data)) {
        $isValid = false;
        $message = "Mobile Number already exists!";
    }
}

echo json_encode(['success' => $isValid, 'message' => $message]);
