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
?>
<?php
include_once "classes/Pdo.php";
$pdo = new Pdo_();
$role_id  = $_POST['role_id'];
$permissions = $_POST['permissions'];

// Usuń wszystkie uprawnienia roli
$pdo->remove_all_role_permissions($role_id);

// Dodaj zaznaczone uprawnienia
foreach($permissions as $permission_id){
    $pdo->add_role_permission($role_id, $permission_id);
}

header("Location: role_permissions.php?role_id={$role_id}");
exit;
?>