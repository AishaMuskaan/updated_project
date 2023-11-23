<header class="header">
    <div class="flex">
        <a href="home.php" class="logo"><img src="../img/logo.png"></a>
        <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="view_products.php">Product</a>
            <a href="order.php">Order</a>
            <a href="about.php">About us</a>
            <a href="contact.php">Contact us</a>
        </nav>
        <div class="icons">
            <i class="bx bxs-user " id="user-btn" ></i>
            <?php
                $count_wishlist_items = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ?");
                $count_wishlist_items->execute([$user_id]);
                $total_wishlist_items = $count_wishlist_items->rowCount();
            ?>
            <a href="wishlist.php" class="cart-btn"><i class="bx bx-heart"></i><sup style="left:96.8%"><?=$total_wishlist_items ?>
            </sup></a>
            <?php
                $count_cart_items = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
                $count_cart_items->execute([$user_id]);
                $total_cart_items = $count_cart_items->rowCount();
            ?>
            <a href="cart.php" class="cart-btn"><i class="bx bx-cart-download"></i><sup style="left:99%"><?=$total_cart_items ?>
            </sup></a>
            <i class="bx bx-list-plus" id="menu-btn" style="font-size: 2rem;"></i>
        </div>
        <div class="user-box">
            <p>Username : <span><?php //echo $_SESSION['user_name']; ?></span></p>
            <p>Email : <span><?php //echo $_SESSION['user_email']; ?></span></p>   
            <a href="login.php" class="btn">login</a>
            <a href="register.php" class="btn">register</a>
            <from method="post" action="">
                <button type="submit" name="logout" class="logout-btn">log out</button>
            </from>
        </div>    
    </div>    
</header>  