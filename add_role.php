<?php
session_start();

if (isset($_SESSION['session_expire'])) {
    if (time() - $_SESSION['session_expire'] > (60 * 5)) {
        session_destroy();
    } else {
        $_SESSION['session_expire'] = time();
    }
}
if (empty($_SESSION['permissions'][1])) {
    die;
}
?><h5><?php
    if (!empty($_SESSION['login'])) {
        echo $_SESSION['login'];
    } else {
        echo 'niezalogowany';
    }
?></h5><?php
include_once "classes/Pdo.php";
$pdo = new Pdo_();
$role_name = $_POST['role_name'];

$pdo->add_role($role_name);

header("Location: roles_list.php");
exit;
?>