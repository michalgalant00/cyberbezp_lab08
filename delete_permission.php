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
//require 'pdo.php';

$pdo = new Pdo_();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $permission_id = $_POST['permission_id'];
    $pdo->remove_permission($permission_id);
    header("Location: permissions_list.php");
    exit();
}


$permissions = $pdo->get_permissions();
?>

<!DOCTYPE html>
<html>
<body>
    <form method="post">
        <label for="permission_id">Wybierz uprawnienie do usunięcia:</label>
        <select id="permission_id" name="permission_id">
        <?php foreach($permissions as $permission): ?>
            <option value="<?php echo $permission['id']; ?>"><?php echo $permission['name']; ?></option>
        <?php endforeach; ?>
        </select>
        <input type="submit" value="Usuń uprawnienie">
    </form>
</body>
</html>