<?php
require_once("DataBase.php");

if(!(isset($_POST['login']) && isset($_POST['password']))) {
    return false;
} else {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $db = new DataBase();
    $users = $db->load_authorization_info($login);
    if(count($users) == 0) {
        exit(json_encode(array('error'=>"Authorization failed, check your login or password.")));
    } else {
        foreach ($users as $user_str) {
            $user = explode(',',trim($user_str, "()"));
            //
            //
            //
            if($user[1] == $password) {
            //
            //
            //
                //создаём хэш
                $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $hash = '';
                for ($i = 0; $i < 10; $i++) {
                    $hash .= $chars[rand(0, count($chars) - 1)];
                }
                $hash = sha1($hash);
                //записываем его в базу
                $db->update_realtor_hash($user[0], $hash);
                //создаём куки
                setcookie("realtor_uid", $user[0], time() + 60*60*24*30);
                setcookie("realtor_hash", $hash, time() + 60*60*24*30);
                //готово
                // header('Location: '.$_SERVER['REMOTE_ADDR'].'/catalog.html');
                exit(json_encode(['success' => true]));
            }
        }
        exit(json_encode(['error'=>"Authorization failed, check your login or password."]));
    }
}
?>