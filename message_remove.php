<?php
session_start();

if (isset($_SESSION['session_expire'])) {
    if (time() - $_SESSION['session_expire'] > (60 * 5)) {
        session_destroy();
    } else {
        $_SESSION['session_expire'] = time();
    }
}

if (empty($_SESSION['permissions'][10])) {
    die;
}

?><h5>
    <?php
        if (!empty($_SESSION['login'])) {
            echo $_SESSION['login'];
        } else {
            echo 'niezalogowany';
        }
        ?></h5>
        
        
        
        <?php
        include_once "classes/Page.php";
        include_once "classes/Pdo.php";

        Page::display_header("Remove message");

        if (isset($_POST['id'])) {
            $pdo = new Pdo_();

            $deleted = $pdo->remove_message($_POST['id']);

            if (!$deleted) {
                echo 'MESSAGE NOT FOUND';
            } else {
                echo 'MESSAGE DELETED';
            }
        }

        ?>
<hr>
 
<hr>
<P>Navigation</P>
<?php Page::display_navigation(); ?>
</body>

</html>