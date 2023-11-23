<?php
include '../components/connection.php';
session_start();
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}else{
    $user_id = '';
}

if(isset($_POST['logout'])) {
    session_destroy();
    header('location: login.php');
}

//adding products in wishlist
if(isset($_POST['add_to_wishlist'])){
    $id = unique_id();
    $product_id = $_POST['product_id'];

    $varify_wishlist = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $varify_wishlist->execute([$user_id, $product_id]);

    $cart_num = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $cart_num->execute([$user_id, $product_id]);

    if($varify_wishlist->rowCount() > 0){
        $warning_msg[] = 'product already exist in your wishlist';
    }else if($cart_num->rowCount() > 0) {
        $warning_msg[] = 'product already exist in your cart';
    }else{
        $select_price = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $select_price->execute([$product_id]);
        $fetch_price = $select_price->fetch(PDO:: FETCH_ASSOC);

        $insert_wishlist = $conn->prepare("INSERT INTO wishlist (id, user_id, product_id, price) VALUES(?,?,?,?)");
        $insert_wishlist->execute([$id, $user_id, $product_id, $fetch_price['price']]);
        $success_msg[] =  'product added to wishlist sucessfully';
    }
}

//adding products in cart

if(isset($_POST['add_to_cart'])){
    $id = unique_id();
    $product_id = $_POST['product_id'];

    $qty = $_POST['qty'];
    $qty = filter_var($qty, FILTER_SANITIZE_STRING);

    $varify_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $varify_cart->execute([$user_id, $product_id]);

    $max_cart_items = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $max_cart_items->execute([$user_id]);

    if($varify_cart->rowCount() > 0){
        $warning_msg[] = 'product already exist in your wishlist';
    }else if($max_cart_items->rowCount() > 20) {
        $warning_msg[] = 'cart is full';
    }else{
        $select_price = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $select_price->execute([$product_id]);
        $fetch_price = $select_price->fetch(PDO:: FETCH_ASSOC);

        $insert_cart = $conn->prepare("INSERT INTO cart (id, user_id, product_id, price, qty) VALUES(?,?,?,?,?)");
        $insert_cart->execute([$id, $user_id, $product_id, $fetch_price['price'], 1]);
        $success_msg[] =  'product added to cart sucessfully';
    }
}

?>
<style type="text/css">
    <?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset = "UTF-8">
    <meta charset = "viewport" content="width-device-width, initial-scale=1.0">
    <link href = 'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
    <title> Green Coffee - product_detail page </title>
</head>
<body>
    <?php include '../components/user_header.php'; ?>
    <div class = "main">
        <div class = "banner">
            <h1> product_detail </h1>
        </div>
        <div class = "title2">
            <a href = "home.php"> home </a><span>/ product detail </span>
        </div>
        <section class="view_page">
            <?php 
            if(isset($_GET['pid'])){
                $pid = $_GET['pid'];
                $select_products = $conn->prepare("SELECT * FROM products  WHERE id = '$pid'");
                $select_products->execute();
                if($select_products->rowCount() > 0 ){
                    while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
                
            ?>
            <form method="post">
                <img src="../image/<?php echo $fetch_products['image'];?>" class="img">
                <div class="detail">
                    <div class="price"><?php echo $fetch_products['price']; ?>Rs/-</div>
                    <div class="name"><?php echo $fetch_products['name']; ?></div>
                    <div class="detail"> <?php echo $fetch_products['product_detail']; ?> </div>

                    <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>" >
                    <div class="button">

                    <button type="submit" name="add_to_wishlist" class="btn"> add to wishlist<i class="bx bx-heart"></i>
                </button>
                <input type="hidden" name="qty" value="1" min="0" class="quantity">
                <button type="submit" name="add_to_cart" class="btn"> add to cart<i class="bx bx-cart"></i>
                </button>
                    </div>
                </div>
            </form>
            <?PHP 
                    }
                }
            }
            ?>
           
        </section>
        <?php include '../components/user_footer.php'; ?>
    </div>
    <script scr ="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script scr ="script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>
</html>