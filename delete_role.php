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
    $role_id = $_POST['role_id'];
    $pdo->remove_role($role_id);
    header("Location: roles_list.php");
    exit();
}

$roles = $pdo->get_roles();
?>

<!DOCTYPE html>
<html>
<body>
    <form method="post">
        <label for="role_id">Wybierz rolę do usunięcia:</label>
        <select id="role_id" name="role_id">
        <?php foreach($roles as $role): ?>
            <option value="<?php echo $role['id']; ?>"><?php echo $role['name']; ?></option>
        <?php endforeach; ?>
        </select>
        <input type="submit" value="Usuń rolę">
    </form>
</body>
</html>