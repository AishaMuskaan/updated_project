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
    

    //update product in cart
    if (isset($_POST['Update_cart'])){
        $cart_id = $_POST['cart_id'];
        $cart_id = filter_var($cart_id, FILTER_SANITIZE_STRING);
        $qty = $_POST['qty'];
        $qty = filter_var($qty, FILTER_SANITIZE_STRING);

        $update_qty = $conn->prepare("UPDATE cart SET qty = ? WHERE id = ?");
        $update_qty->execute([$qty, $cart_id]);

        $success_msg[] = 'cart quantity updated successfully';
    }


    //delete item from delete
    if (isset($_POST['Delete_item'])){
        $cart_id = $_POST['cart_id'];
        $cart_id = filter_var($cart_id, FILTER_SANITIZE_STRING);

        $varify_delete_items = $conn->prepare("SELECT * FROM cart WHERE id = ?");
        $varify_delete_items->execute([$cart_id]);

        if($varify_delete_items->rowCount() > 0){
            $delete_cart_id = $conn->prepare("DELETE FROM cart WHERE id = ?");
            $delete_cart_id->execute([$cart_id]);
            $success_msg[] = "cart item delete successfully";
        }else{
            $warning_msg[] = 'cart item already deleted';
        }
    }

    //empty cart
    if (isset($_POST['empty_cart'])) {
        $verify_empty_items = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
        $verify_empty_items->execute([$user_id]);
    
        if ($verify_empty_items->rowCount() > 0) {
            $delete_cart_items = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $delete_cart_items->execute([$user_id]);
            $success_msg[] = "Cart emptied successfully";
        } else {
            $warning_msg[] = 'Cart items already deleted';
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
    <title>Coffee Shop - Cart page</title>
</head>
<body>
    <?php include '../components/user_header.php'; ?>
    <div class = "main">
        <div class = "banner">
            <h1> my cart </h1>
        </div>
        <div class = "title2">
            <a href = "home.php"> home </a><span>/ cart </span>
        </div>
        <section class="products">
            <h1 class="title">products added in cart</h1>
            <div class="box-container">
            <?php
                    $grand_total = 0;
                    $select_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
                    $select_cart->execute([$user_id]);
                    if($select_cart->rowCount() > 0 ){
                        while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                            $select_products = $conn->prepare("SELECT * FROM products WHERE id = ?");
                            $select_products->execute([$fetch_cart['product_id']]);
                            if ($select_products->rowCount() > 0){
                                $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)
                                
                ?>
                <form method = "post" action = "" class = "box">
                    <input type = "hidden" name = "cart_id" value = "<?=$fetch_cart['id']; ?>">
                    <div style="text-align:center" >
                        <img src = "../image/<?=$fetch_products['image']; ?>" class = "img">
                    </div>
                    <h3 class = "name"><?=$fetch_products['name']; ?></h3>
                    <div class = "flex">
                        <p class = "price">price <?=$fetch_products['price']; ?>Rs/-</p>
                        <input type="number" name="qty" required min="1" value="<?=$fetch_cart['qty']; ?>"
                         max="99" maxlength="2" class="qty">
                        <button type="submit" name="Update_cart" class="bx bxs-edit fa-edit"></button> 
                    </div>
                    <p class = "sub-total">sub total : <span><?=$fetch_cart['qty'] * $fetch_cart['price']
                     ?>Rs</span></p>
                    <button type="submit" name="Delete_item" class="btn" onclick = "return confirm('delete this item')">
                    delete</button>
                </form>       
                <?php  
                            $grand_total+=$fetch_cart['qty'] * $fetch_cart['price'];           
                            }else{
                                echo '<p class="empty">product was not found!</p>';
                            }
                        }
                    }else{
                        echo '<p class="empty">no products added yet!</p>';
                    }            
                ?>
            </div>
            <?php
                if($grand_total !=0){
            ?>
            <div class = "cart-total">
                <p>total amount payable : <span><?=$grand_total; ?>Rs/-</span></p>
                <div class = "button">
                    <from method ="post" action="">
                        <button type = "submit" name = "empty_cart" class = "btn" onclick = "return confirm
                         ('are you sure to empty your cart')">empty cart</button>
                    </form>
                    <a href = "checkout.php" class = "btn">proceed to checkout</a>
                </div>
            </div>
            <?php } ?>
        </section>
        <?php include '../components/user_footer.php'; ?>
    </div>
    <script scr ="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script scr ="script .js"></script>
    <?php include '../components/alert.php'; ?>
</body>
</html>