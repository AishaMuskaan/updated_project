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
        <title> Coffee Shop - About Us Page</title>
    </head>
    <body>
    <?php include '../components/user_header.php'; ?>
    <div class = "main">
        <div class = "banner">
            <h1> about us </h1>
        </div>
        <div class = "title2">
            <a href = "home.php">Home </a><span>/ about</span>
        </div>
        <div class = "about-category">
            <div class = "box">
                <img src = "../img/black_roast.png">
                <div class = "detail">
                    <span>Coffee</span>
                    <h1>lemon green</h1>
                    <a href = "view_products.php" class = "btn">shop now</a>
                </div>
            </div>
            <div class = "box">
                <img src = "../img/italian_roast.png">
                <div class = "detail">
                    <span>Coffee</span>
                    <h1>lemon Teaname</h1>
                    <a href = "view_products.php" class = "btn">shop now</a>
                </div>
            </div>
            <div class = "box">
                <img src = "../img/2.webp">
                <div class = "detail">
                    <span>Coffee</span>
                    <h1>lemon Teaname</h1>
                    <a href = "view_products.php" class = "btn">shop now</a>
                </div>
            </div>
            <div class = "box">
                <img src = "../img/1.webp">
                <div class = "detail">
                    <span>Coffee</span>
                    <h1>lemon green</h1>
                    <a href = "view_products.php" class = "btn">shop now</a>
                </div>
            </div>
        </div>
        <section class = "services">
            <div class = "title">
                <img src = "../img/download.png" class="logo">
                <h1>why choose us</h1>
                <p>Choose us for an unparalleled coffee journey; our commitment to sourcing the finest beans, 
                    meticulous roasting process, and dedication to delivering a rich and flavorful experience 
                    make us your ultimate destination for exceptional coffee products.</p></br>
            </div>
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
        <div class = "about">
            <div class = "row">
                <div class ="../img-box">
                    <img src = "../img/3.png">
                </div>
                <div class = "detail">
                    <h1>Visit our beautiful showroom</h1>
                    <p>Embark on a sensory adventure – visit our exquisite showroom and immerse yourself in the
                         world of premium coffee, where quality meets craftsmanship, and indulge in the finest
                          selection of coffee products.
                    </p>
                    <a href = "view_products.php" class = "btn">shop now</a>
                </div>
            </div>
        </div>
        <div class = "testimonial-container">
            <div class = "title">
                <img src = "../img/download.png" class = "logo">
                <h1>what people say about us</h1>
                <p style="margin:2vw">Discover the buzz around our brand—customer testimonials that reflect the
                 exceptional quality and satisfaction found in every sip of our premium coffee products.
                </p>
            </div>    
                <div class = "container">
                    <div class = "testimonial-item active">
                        <img src = "../img/Afshan.jpg">
                        <h1>Afshan Azhar</h1>
                        <p>Meet the visionary behind our coffee haven, Afshan, an entrepreneur driven 
                            by a passion for exceptional coffee. With a keen eye for quality and a commitment 
                            to creating a unique coffee experience, she leads our team in curating
                             the finest coffee products. Join us on this journey as we share a cup brewed from 
                             Afshan's dedication and love for the perfect coffee blend.
                        </p>
                    </div>
                    <div class = "testimonial-item">
                        <img src = "../img/Aisha.jpg">
                        <h1>Aisha Shahid</h1>
                        <p>Meet Aisha, a vital force in our passionate team. With unwavering 
                            dedication, she elevates our coffee experience, ensuring each product reflects
                             the highest standards. Join us in savoring Aisha's distinctive 
                             touch, making every cup a testament to their dedication and craft.
                        </p>
                    </div>
                    <div class = "testimonial-item">
                        <img src = "../img/Maheen.png">
                        <h1>Maheen Masood</h1>
                        <p>Introducing Maheen, an integral part of our passionate team. With unwavering 
                            dedication, she elevates our coffee experience, ensuring each product reflects
                             the highest standards. Join us in savoring Maheen's distinctive 
                             touch, making every cup a testament to their dedication and craft.
                        </p>
                    </div>
                    <div class = "testimonial-item">
                        <img src = "../img/Khadija.jpg">
                        <h1>Khadija Jabeen</h1>
                        <p>Meet Khadija, a vital force in our passionate team. With unwavering 
                            dedication, she elevates our coffee experience, ensuring each product reflects
                             the highest standards. Join us in savoring Khadija's distinctive 
                             touch, making every cup a testament to their dedication and craft.
                        </p>
                    </div>
                    <div class = "left-arrow" onclick="nextSlide()"><i class = "bx bxs-left-arrow-alt"></i></div>
                    <div class = "right-arrow" onclick="prevSlide()"><i class = "bx bxs-right-arrow-alt"></i></div>
                </div>    
        </div>
        <?php include '../components/user_footer.php'; ?>
    </div>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalart/2.1.2/sweetalart.min.js"></script>
    <script src="script.js"></script>
    <script type="text/javascript">
        let slides  = document.querySelectorAll('.testimonial-item');
        let index = 0;
        
        function  nextSlide(){
            slides[index].classList.remove('active');
            index = (index + 1) % slides.length;
            slides[index].classList.add('active');
        }
        function prevSlide(){
            slides[index].classList.remove('active');
            index = (index - 1 + slides.length) % slides.length;
            slides[index].classList.add('active');
        }
    </script>
    <?php include '../components/alert.php'; ?>
    </body>
</html>
