<?php

namespace Application\Model;
use Application\Component\DB;
use Application\Core\Model;

class ModelUser extends Model
{
    public function register_user($login, $password)
    {
        $db = new DB();
        $con=$db->connect();

        $check_query = "SELECT COUNT(*) as count FROM Users WHERE user_name = ?";
        $stmt = $con->prepare($check_query);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];

// Якщо користувач з таким логіном уже існує, повернути помилку
        if ($count > 0) {
            return false;
        }


        $stmt = $con->prepare("INSERT INTO Users (user_name, password) VALUES (?, ?)");
        $stmt->bind_param('ss', $login, $password);

        $stmt->execute();
        $stmt->close();
        return true;
    }
    public function auth($login, $password){
        $db = new DB();
        $con=$db->connect();

        $stmt = $con->prepare('SELECT * FROM Users WHERE user_name = ? AND password = ?');
        $stmt->bind_param('ss', $login, $password);

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows === 1) {
            $_SESSION['user_id'] = $result->fetch_array()['id'];
//            header('Location: profile.php');
            return true;
        } else {
            return false;
        }
    }
    function is_user_logined(){
        if(!empty($_SESSION['user_id'])){
            return $_SESSION['user_id'];
        }
        else{
            return false;
        }
    }
    function logout(){
        session_start();
        session_destroy();

        // Перенаправити користувача на іншу сторінку
        header('Location: /');
        exit();
    }
}