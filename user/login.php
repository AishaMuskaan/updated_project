<?php
    include '../components/connection.php';

    session_start();
    if(isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }else{
        $user_id = '';
    }    

    //login user
    if(isset($_POST['submit'])){
    
    $email = $_POST['email'];
    $email = filter_var($email,FILTER_SANITIZE_STRING);

    $pass = $_POST['pass'];
    $pass = filter_var($pass,FILTER_SANITIZE_STRING);

    $select_user=$conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $select_user->execute([$email, $pass]);
    $row=$select_user->fetch(PDO::FETCH_ASSOC);

        if($select_user->rowCount() > 0){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            header('location: home.php');
        }else{
            $message[]='incorrect username or password';
        }
    }
?>
<style type="text/css">
    <?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="Viewport" content="width=device-width, initial-scale=1.0">
        <title>Coffee Shop user panel - login now </title>
</head>
<body>
    <div class="main-container">
    <section class="form-container">
            <div class="title"> 
                <img src="../img/download.png">
                <h1>Login now</h1>
                <p> Unlock a world of coffee delights with a single login to our intuitive and secure portal.</p>
            </div>
            <form action="" method="post">   
            <div class="input-field">
                    <p>your email<sup>*</sup></p>
                    <input type="email" name="email" maxlenght="50" required placeholder="Enter your email"
                     oninput="this.value=this.value.replace(/\s/g,'')">
            </div>
            <div class="input-field">
                    <p>your password<sup>*</sup></p>
                    <input type="password" name="pass" maxlenght="50" required placeholder="Enter your password"
                     oninput="this.value=this.value.replace(/\s/g,'')">
            </div>
                    <input type="Submit" name="submit" value="Login" class="btn">
                    <p>do not have an account? <a href="register.php">register now</a></p>
            </form>
    </section>
    </div>
    <script src="../components/sweetalert.js"></script>
    <script scr="script.js"></script>
    <?php include '../components/alert.php';?>
</body>
</html>