<?php
require_once __DIR__ . '/../../../bootstrap.php';
session_start();
use CT275\Project\User;
$user = new User($PDO);
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['id'])
    && ($user->find($_POST['id'])) !== null
) {
    $user->delete();
}
redirect('./../admin.php');