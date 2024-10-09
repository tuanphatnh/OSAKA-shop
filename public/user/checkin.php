<?php
session_start();

$response = [];
$authenticated = false;

if (isset($_SESSION['user']) && $_SESSION['user'] == '0') {
    $authenticated = true;
}

$response['authenticated'] = $authenticated;

header('Content-Type: application/json');
echo json_encode($response);
?>