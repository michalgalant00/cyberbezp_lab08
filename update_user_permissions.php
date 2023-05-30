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
}?>
<?php
include_once "classes/Pdo.php";
$pdo = new Pdo_();
$user_id = $_POST['user_id']; // Zastąp tym konkretnym ID użytkownika
$permissions = $_POST['permissions'];

// Usuń wszystkie uprawnienia użytkownika
$pdo->remove_all_user_permissions($user_id);

// Dodaj zaznaczone uprawnienia
foreach($permissions as $permission_id){
    $pdo->add_user_permission($user_id, $permission_id);
}

header("Location: user_permissions.php?user_id={$user_id}");
exit;
?>