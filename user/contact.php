<?php
    include '../components/connection.php';
    session_start();
    if(isset($_SESSION['user_id'])) {
          $user_id = $_SESSION['user_id'];
      }else{
          $user_id = '';
      } 
      if(isset($_POST['logout'])){
         session_destroy();
         header("location: login.php");
      }

      if(isset($_POST['submit-btn'])){
    
        $id = unique_id();
        $subject = $_POST['subject'];
        $subject = filter_var( $subject, FILTER_SANITIZE_STRING);
    
        $message = $_POST['message'];
        $message = filter_var($message, FILTER_SANITIZE_STRING);

        $get_userID = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $get_userID->execute([$user_id]);
        $fetch_userID =  $get_userID->fetch(PDO::FETCH_ASSOC);

        $insert_message = $conn->prepare("INSERT INTO message (id,user_id,subject,message) VALUES(?,?,?,?)");
        $insert_message->execute([$id,$fetch_userID['id'],$subject,$message]);
        
    
      }
?>
<style type="text/css">
    <?php include 'style.css'; ?>
</style>    
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <title> Coffee Shop - Contact Us Page</title>
    </head>
    <body>
    <?php include '../components/user_header.php'; ?>
    <div class = "main">
        <div class = "banner">
            <h1> contact us </h1>
        </div>
        <div class = "title2">
            <a href = "home.php">Home </a><span>/ contact us</span>
        </div>
        <section class = "services">
            <div class = "box-container">
                <div class = "box">
                    <img src = "../img/icon2.png">
                    <div class ="detail">
                        <h3>Great savings</h3>
                        <p>save big every order</p>
                    </div>
                </div>
                <div class = "box">
                    <img src = "../img/icon1.png">
                    <div class ="detail">
                        <h3>24*7 support</h3>
                        <p>one-on-one support</p>
                    </div>
                </div>
                <div class = "box">
                    <img src = "../img/icon0.png">
                    <div class ="detail">
                        <h3>Gifts vouchers</h3>
                        <p>vouchers on every festivals</p>
                    </div>
                </div>
                <div class = "box">
                    <img src = "../img/icon.png">
                    <div class ="detail">
                        <h3>Worldwide delivery</h3>
                        <p>dropship worldwide</p>
                    </div>
                </div>
            </div>
        </section>
        <?php
            $get_userInfo = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $get_userInfo->execute([$user_id]);
            $fetch_userInfo = $get_userInfo->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="form-container">
            <form method = "post">
                <div class="title">
                    <img src="../img/download.png" class="logo">
                    <h1> leave a message </h1>
                </div>
                <div class="input-field">
                    <p> your name <sup>*</sup></p>
                    <input type="text" name="name" value="<?=$fetch_userInfo['name']?>"  readonly>
                </div>
                <div class="input-field">
                    <p> your email <sup>*</sup></p>
                    <input type="email" name="email" value="<?=$fetch_userInfo['email']?>" readonly>
                </div>
                <div class="input-field">
                    <p> your subject <sup>*</sup></p>
                    <input type="text" name="subject">
                </div>
                <div class="input-field">
                    <p> your message <sup>*</sup></p>
                    <textarea name="message"></textarea>
                </div>
                <button type="submit" name="submit-btn" class="btn"> send message</button>
            </form>
        </div>     
        <div class="address">
                <div class="title">
                    <img src="../img/download.png" class="logo">
                    <h1> contact detail </h1>
                    <p>Contact us for a personalized coffee experience - our dedicated team is here to assist 
                        you.</p>
                </div>
                <div class="box-container">
                    <div class="box">
                        <i class="bx bxs-map-pin"></i>
                        <div>
                            <h4>address</h4>
                            <p>1002 Merigold Lane, Karachi</p>
                        </div>
                    </div>
                    <div class="box">
                        <i class="bx bxs-phone-call"></i>
                        <div>
                            <h4>phone number</h4>
                            <p>8866889955</p>
                        </div>
                    </div>
                    <div class="box">
                        <i class="bx bxs-map-pin"></i>
                        <div>
                            <h4>email</h4>
                            <p>selenaAnsari@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>  
        <?php include '../components/user_footer.php'; ?>
    </div>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalart/2.1.2/sweetalart.min.js"></script>
    <script src="script.js"></script>
    <?php include '../components/alert.php'; ?>
    </body>
</html>