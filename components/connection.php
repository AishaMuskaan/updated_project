<?php
    $db_name = 'mysql:host=localhost;dbname=coffee_shop';
    $user_name ='root';
    $user_passward = '';

    $conn = new PDO($db_name,$user_name,$user_passward);

    if(!$conn){
        echo "did not connect to database";
    }
   

    function unique_id(){
        $chars= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strLen($chars);
        $randomString = '';
        for($i=0; $i < 20; $i++){
            $randomString.=$chars[mt_rand(0, $charLength - 1)];
        }
        return $randomString;
    }
?>