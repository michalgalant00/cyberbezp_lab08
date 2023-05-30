<?php
require './htmlpurifier-4.15.0/library/HTMLPurifier.auto.php';
require 'Aes.php';
require './email.php';

class Pdo_
{
 private $db;
 private $purifier;

    private $aes;
    private $log_2F_step1;

    private $mail;

 public function __construct() {
 $config = HTMLPurifier_Config::createDefault();
 $this->purifier = new HTMLPurifier($config);
 try {
 $this->db = new PDO('mysql:host=localhost;dbname=news', 'root', '');

 $this->aes = new Aes();
    //$test = $this->aes->encrypt('test');
    // $test;

    //echo '</br></br>';
    //echo $this->aes->decrypt($test);
$this->mail = new M(); 

 }catch (PDOException $e){
 // add relevant code
 die();
 }
 }

 public function log_2F_step2($login,$code){
    $login=$this->purifier->purify($login);
    $code=$this->purifier->purify($code);
    try {
    $sql="SELECT id,login,sms_code,code_timelife
   FROM user WHERE login=:login";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['login'=>$login]);
    $user_data=$stmt->fetch();
    if($code==$user_data['sms_code']
   &&time()< strtotime($user_data['code_timelife'])){
    //login successfull
    echo 'Login successfull<BR/>';

    $_SESSION['login'] = $user_data['login'];
    
    $_SESSION['session_expire'] = time();


    $this->save_sing_in($user_data['login']);
    $this->add_permissions_and_roles_to_user_session($user_data['user_id']);

    

    return true;
    }else{
    echo 'login FAILED<BR/>';
    return false;
    }
    } catch (Exception $e) {
    print 'Exception' . $e->getMessage();
    }
    }
    public function get_permissions() {
        $stmt = $this->db->query('SELECT * FROM permissions');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_user_permissions($user_id) {
        // Upewnij się, że user_id jest liczbą
        if (!is_numeric($user_id)) {
            throw new InvalidArgumentException('User ID must be a number.');
        }
        
        // Zapytanie SQL do pobrania uprawnień użytkownika
        $sql = "SELECT permissions.id, permissions.name 
                FROM permissions 
                JOIN user_permission ON permissions.id = user_permission.permission_id 
                WHERE user_permission.user_id = :user_id";
        
        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Zwróć wyniki jako tablicę asocjacyjną
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function add_user_permission($user_id, $permission_id) {
        // Upewnij się, że user_id i permission_id są liczbami
        if (!is_numeric($user_id) || !is_numeric($permission_id)) {
            throw new InvalidArgumentException('User ID and Permission ID must be numbers.');
        }
        
        // Zapytanie SQL do dodania uprawnienia dla użytkownika
        $sql = "INSERT INTO user_permission (user_id, permission_id) VALUES (:user_id, :permission_id)";
        
        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);
        
        // Wykonaj zapytanie i zwróć wynik
        return $stmt->execute();
    }

    public function get_roles() {
        // Zapytanie SQL do pobrania wszystkich ról
        $sql = "SELECT * FROM roles";
        
        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        // Pobierz wyniki zapytania
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Zwróć wyniki
        return $roles;
    }

    public function get_role_permissions($role_id) {
        // Zapytanie SQL do pobrania uprawnień dla określonej roli
        $sql = "SELECT p.id, p.name FROM permissions p
                JOIN role_permission rp ON p.id = rp.permission_id
                WHERE rp.role_id = :role_id";
        
        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Pobierz wyniki zapytania
        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Zwróć wyniki
        return $permissions;
    }

    public function add_role_permission($role_id, $permission_id) {
        // Zapytanie SQL do dodania uprawnienia do roli
        $sql = "INSERT INTO role_permission (role_id, permission_id) VALUES (:role_id, :permission_id)";
        
        echo '  ';
        echo $sql;
        echo " $role_id  $permission_id";
        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindValue(':permission_id', $permission_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Sprawdź, czy zapytanie zostało pomyślnie wykonane
        if($stmt->rowCount() > 0){
            return true; // Uprawnienie zostało pomyślnie dodane
        } else {
            return false; // Coś poszło nie tak
        }
    }

    public function remove_user_permission($user_id, $permission_id) {
        // Zapytanie SQL do usunięcia uprawnienia od użytkownika
        $sql = "DELETE FROM user_permission WHERE user_id = :user_id AND permission_id = :permission_id";
    
        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':permission_id', $permission_id, PDO::PARAM_INT);
        $stmt->execute();
    
        // Sprawdź, czy zapytanie zostało pomyślnie wykonane
        if($stmt->rowCount() > 0){
            return true; // Uprawnienie zostało pomyślnie usunięte
        } else {
            return false; // Coś poszło nie tak
        }
    }

    public function remove_role_permission($role_id, $permission_id) {
        // Zapytanie SQL do usunięcia uprawnienia od roli
        $sql = "DELETE FROM role_permission WHERE role_id = :role_id AND permission_id = :permission_id";
    
        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindValue(':permission_id', $permission_id, PDO::PARAM_INT);
        $stmt->execute();
    
        // Sprawdź, czy zapytanie zostało pomyślnie wykonane
        if($stmt->rowCount() > 0){
            return true; // Uprawnienie zostało pomyślnie usunięte z roli
        } else {
            return false; // Coś poszło nie tak
        }
    }

    
    public function get_user_roles($user_id) {
        // Zapytanie SQL do pobrania ról użytkownika
        $sql = "SELECT r.id, r.name 
                FROM roles r 
                INNER JOIN user_role ur ON r.id = ur.role_id 
                WHERE ur.user_id = :user_id";
    
        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    
        $stmt->execute();
    
        // Pobierz wyniki zapytania
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $result;
    }

    public function remove_all_user_permissions($user_id){
        $sql = "DELETE FROM user_permission WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function add_role($role_name){
        $sql = "INSERT INTO roles (name) VALUES (:role_name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role_name', $role_name, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function remove_all_role_permissions($role_id){
        $sql = "DELETE FROM role_permission WHERE role_id = :role_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function remove_all_user_roles($user_id){
        $sql = "DELETE FROM user_role WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

 public function add_user($login,$email,$password, $twofa){
    //generate salt
$salt = random_bytes(16);
$pepper = 'cyberbezpieczenstwo';
$password = hash('sha512', $password . $salt);

//echo $password . $salt; 
 $login=$this->purifier->purify($login);
 $email=$this->purifier->purify($email);
    
 //$password = $this->aes->encrypt($password);

 try {
 $sql="INSERT INTO `user`( `login`, `email`, `hash`, `salt`, `id_status`, `password_form`, `2fa`)
 VALUES (:login,:email,:hash,:salt,:id_status,:password_form,:2fa)";
 //hash password
//  $password = hash('sha512', $password);
 $data= [
 'login' =>$login,
 'email' =>$email,
 'hash' =>$password,
 'salt' => $salt,
 'id_status'=>'1',
 'password_form'=>'1',
 '2fa'=> (int) $twofa,
 ];
 $this->db->prepare($sql)->execute($data);
 } catch (Exception $e) {
//modify the code here
 print 'Exception' . $e->getMessage();
 }
 }
 public function add_user_role($user_id, $role_id) {
    // Zapytanie SQL do dodania roli do użytkownika
    $sql = "INSERT INTO user_role (user_id, role_id) VALUES (:user_id, :role_id)";
    // Przygotuj i wykonaj zapytanie
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
    $stmt->execute();
}
public function remove_user_role($user_id, $role_id) {
    // Zapytanie SQL do usunięcia roli od użytkownika
    $sql = "DELETE FROM user_role WHERE user_id = :user_id AND role_id = :role_id";

    // Przygotuj i wykonaj zapytanie
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
    $stmt->execute();
}


public function change_password($old_password, $password, $password2) {

    if ($password != $password2) {
        echo 'Passwords are not same';
        return false;
    }

    $login = $_SESSION['login'];

    $sql = "SELECT hash, salt FROM user WHERE login=:login";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['login'=>$login]);
    $user_data=$stmt->fetch();


    $old_hash = hash('sha512', $old_password . $user_data['salt']);
        
    if ($old_hash != $user_data['hash']) {
        echo 'Old password is not correct';
        return false;
    }
    

    $salt = random_bytes(16);

    $hash = hash('sha512', $password . $salt);
    //$hash= $this->aes->encrypt($hash);

    $sql = "UPDATE user SET hash=:hash, salt=:salt WHERE login=:login";

    $data= [
        'login' =>$login,
        'hash' => $hash,
        'salt' => $salt,
    ];

    $this->db->prepare($sql)->execute($data);

    echo 'Password changed';
}
public function log_2F_step1($login,$password){
    $login=$this->purifier->purify($login);
    try {
    $sql="SELECT id,hash,login,salt,email,2fa FROM user WHERE login=:login";
    $stmt= $this->db->prepare($sql);
    $stmt->execute(['login'=>$login]);
    $user_data=$stmt->fetch();
    $password = hash('sha512', $password . $user_data['salt']);

    if (!$user_data['2fa']) {
                // echo 'Login successfull<BR/>';

        if ($password == $user_data['hash']) {
            $result = [
                'result' => 'logged_in',
                'user_id' => $user_data['id']
            ];

            return $result;
        }
        else {
            echo 'login FAILED<BR/>';
            $result = [
                'result' => 'failed'
            ];
            return $result;
        }
    }

    if($password==$user_data['hash']){

       
    //generate and send OTP
    $otp=random_int(100000, 999999);
    $code_lifetime=date('Y-m-d H:i:s', time()+300);
    try{
    $sql="UPDATE `user` SET `sms_code`=:code,
   `code_timelife`=:lifetime WHERE login=:login";
    $data= [
    'login' =>$login,
    'code' =>$otp,
    'lifetime' =>$code_lifetime
    ];
    $this->db->prepare($sql)->execute($data);

    $this->mail->send_email($user_data['email'], $otp);

    //add the code to send an e-mail with OTP
    $result= [
    'result'=>'success'
    ];
    return $result;
    } catch (Exception $e) {
    print 'Exception' . $e->getMessage();
    //add necessary code here
    }
    }else{
    echo 'login FAILED<BR/>';
    $result= [
    'result'=>'failed'
    ];
    return $result;
    }
    } catch (Exception $e) {
    print 'Exception' . $e->getMessage();
    //add necessary code here
    }
    }

    public function add_permission($permission_name) {
        $stmt = $this->db->prepare("INSERT INTO permissions (name) VALUES (:permission_name)");
        $stmt->bindParam(':permission_name', $permission_name);
        
        try {
            $stmt->execute();
        } catch(PDOException $e) {
            // Możesz tutaj obsłużyć wyjątek, na przykład wyświetlić komunikat o błędzie
            throw $e;
        }
    }

    public function remove_role($role_id) {
        // Usuwanie wszystkich uprawnień przypisanych do roli
        
        $stmt = $this->db->prepare("DELETE FROM role_permission WHERE role_id = :role_id");
        $stmt->execute(['role_id' => $role_id]);
    
        // Usuwanie wszystkich przypisań roli do użytkowników
        $stmt = $this->db->prepare("DELETE FROM user_role WHERE role_id = :role_id");
        $stmt->execute(['role_id' => $role_id]);
    
        // Usuwanie roli
        $stmt = $this->db->prepare("DELETE FROM roles WHERE id = :role_id");
        $stmt->execute(['role_id' => $role_id]);
    }

    public function get_users() {
        $stmt = $this->db->prepare("SELECT id, login FROM user");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save_sing_in($login)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users_sessions (user_id, data_login, hash_session_id) 
            VALUES 
                (
                    (SELECT id FROM user WHERE login = :login LIMIT 1),
                    NOW(),
                    :hash_session_id
                )
        ");
        $stmt->bindParam(':login', $login);
        $hash_session_id = hash('sha256', session_id());
        $stmt->bindParam(':hash_session_id', $hash_session_id);
        
        try {
            $stmt->execute();
        } catch(PDOException $e) {
            // Możesz tutaj obsłużyć wyjątek, na przykład wyświetlić komunikat o błędzie
            throw $e;
        }
    }

    public function save_sing_out()
    {
        $stmt = $this->db->prepare("
            UPDATE users_sessions
            SET data_logout = NOW()
            WHERE hash_session_id = :hash_session_id
        ");

        $hash_session_id = hash('sha256', session_id());
        $stmt->bindParam(':hash_session_id', $hash_session_id);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            // Możesz tutaj obsłużyć wyjątek, na przykład wyświetlić komunikat o błędzie
            throw $e;
        }
    }

    public function remove_permission($permission_id) {
        // Usuwanie wszystkich przypisań uprawnienia do ról
        $stmt = $this->db->prepare("DELETE FROM role_permission WHERE permission_id = :permission_id");
        $stmt->execute(['permission_id' => $permission_id]);
    
        // Usuwanie wszystkich przypisań uprawnienia do użytkowników
        $stmt = $this->db->prepare("DELETE FROM user_permission WHERE permission_id = :permission_id");
        $stmt->execute(['permission_id' => $permission_id]);
    
        // Usuwanie uprawnienia
        $stmt = $this->db->prepare("DELETE FROM permissions WHERE id = :permission_id");
        $stmt->execute(['permission_id' => $permission_id]);
    }

    public function add_permissions_and_roles_to_user_session($user_id)
    {
        $roles = $this->get_user_roles($user_id);


        foreach ($roles as $user_role) {
            $_SESSION['roles'][$user_role['id']] = $user_role;
        }



        $_SESSION['permissions'] = $this->get_user_permissions($user_id);

        $permissions = [];
        foreach ($_SESSION['roles'] as $role) {


            $role_permissions = $this->get_role_permissions($role['id']);

            foreach ($role_permissions as $role_permission) {
                $permissions[$role_permission['id']] = $role_permission;
            }
        }

        foreach ($_SESSION['permissions'] as $permission) {
            $permissions[$permission['id']] = $permission;
        }

        $_SESSION['permissions'] = $permissions;
    }
    

    public function remove_message($message_id) {
        $sql = "DELETE FROM message 
        WHERE id = :message_id";

        // Przygotuj i wykonaj zapytanie
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message_id', $message_id, PDO::PARAM_INT);
        $stmt->execute();

        // Sprawdź, czy zapytanie zostało pomyślnie wykonane
        if ($stmt->rowCount() > 0) {
            return true; // Uprawnienie zostało pomyślnie usunięte
        } else {
            return false; // Coś poszło nie tak
        }
    }

    
 public function log_user_in($login,$password){
 $login=$this->purifier->purify($login);
 try {
 $sql="SELECT id,hash,login,salt FROM user WHERE login=:login";
 $stmt= $this->db->prepare($sql);
 $stmt->execute(['login'=>$login]);
 $user_data=$stmt->fetch();

 //print_r($user_data);

 if (empty($user_data)) {
    echo 'There is no user with login ' . $login;
    return false;
 }

 //echo '-___' . $password . '---';
 $password = hash('sha512', $password . $user_data['salt']);
 
 //echo $password; echo '</br>';

 //print_r($user_data)
//;
 //echo $user_data['hash'];

 //$hash = $this->aes->decrypt($user_data['hash']);

 if($password==$user_data['hash']){
 echo 'login successfull<BR/>';
 echo 'You are logged in as: '.$user_data['login'].'<BR/>';

 $_SESSION['login'] = $user_data['login'];
    $this->save_sing_in($user_data['login']);
}else{
    echo 'login FAILED<BR/>';
    }
    } catch (Exception $e) {
   //modify the code here
    print 'Exception' . $e->getMessage();
    }
    }
   }
   
   