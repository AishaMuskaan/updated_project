<?php
include '../components/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('location: login.php');
}

if (isset($_POST['place_order'])) {
    $id = unique_id();
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country'] . ', ' . $_POST['pincode'], FILTER_SANITIZE_STRING);
    $address_type = filter_var($_POST['address_type'], FILTER_SANITIZE_STRING);
    $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
    $date = date('Y-m-d H:i:s');

    $varify_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $varify_cart->execute([$user_id]);

    $grand_total = 0;


    if (isset($_GET['get_id'])) {
        $productID = $_GET['get_id'];
        echo "Product ID from URL: " . $productID;  // Debugging output
        $get_product = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT  1");
        $get_product->execute([$productID]);
    
        if ($get_product->rowCount() > 0) {
            $fetch_p = $get_product->fetch(PDO::FETCH_ASSOC);
    
            if ($fetch_p) {
                echo "Product information found.";
    
                // Insert the order details
                $insert_order = $conn->prepare("INSERT INTO orders(id, user_id, name, number, email, address, 
                    address_type, method, total_products, total_price, status, date) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
    
                // Assuming the quantity is fixed as 1 for direct purchases
                $quantity = 1;
    
                // Calculate the total price for the single product
                $total_price = $fetch_p['price'] * $quantity;
    
                // Execute the order insertion query
                $result = $insert_order->execute([$id, $user_id, $name, $number, $email, $address, $address_type, 
                    $method, $quantity, $total_price, 'pending', $date]);
    
                if (!$result) {
                    $error_info = $insert_order->errorInfo();
                    error_log("Failed to insert order: " . implode(" | ", $error_info));
                    $warning_msg[] = 'Failed to insert order';
                } else {
                    // Get the last inserted order ID
                    $last_order_id = $conn->lastInsertId();
    
                    // Insert order items
                    $insert_order_items = $conn->prepare("INSERT INTO `order-items` (order_id, product_id, quantity) VALUES(?, ?, ?)");
                    $result_items = $insert_order_items->execute([$id, $fetch_p['id'], $quantity]);
    
                    if (!$result_items) {
                        $error_info = $insert_order_items->errorInfo();
                        error_log("Failed to insert order item: " . implode(" | ", $error_info));
                        $warning_msg[] = 'Failed to insert order item';
                    } else {
                        header('location: order.php');
                    }
                }
            } else {
                $warning_msg[] = 'Product not found';
            }
        } else {
            $warning_msg[] = 'Something went wrong';
        }
    }
    
    
     elseif ($varify_cart->rowCount() > 0) {
        // Create an order record
        $insert_order = $conn->prepare("INSERT INTO orders(id, user_id, name, number, email, address, 
            address_type, method, total_products, total_price, status, date) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
    
        $result = $insert_order->execute([$id, $user_id, $name, $number, $email, $address, $address_type, 
            $method, 0, 0, 'pending', date('Y-m-d H:i:s')]);
    
        if ($result) {
            $grand_total = 0; // Initialize grand total
    
        // Fetch items from the cart
while ($f_cart = $varify_cart->fetch(PDO::FETCH_ASSOC)) {
    // Get product details from the products table
    $get_product_cart = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $get_product_cart->execute([$f_cart['product_id']]);

    if ($get_product_cart->rowCount() > 0) {
        $fetch_cart = $get_product_cart->fetch(PDO::FETCH_ASSOC);

        if ($fetch_cart) {
            // Insert each product as an order item
            $insert_order_items = $conn->prepare("INSERT INTO `order-items` (order_id, product_id, quantity) 
                VALUES(?, ?, ?)");

            if (isset($f_cart['product_id'], $f_cart['qty'])) {
                // Insert query
                $result_items = $insert_order_items->execute([$id, $f_cart['product_id'], $f_cart['qty']]);

                if (!$result_items) {
                    // Handle error
                }
            } else {
                // Handle the case where product_id or qty is not set
                $warning_msg[] = 'Product ID or quantity not found in cart';
            }

            if (!$result_items) {
                $error_info = $insert_order_items->errorInfo();
                error_log("Failed to insert order item: " . implode(" | ", $error_info));
                $warning_msg[] = 'Failed to insert order item';
            }

            // Calculate subtotal and add it to grand total
            $sub_total = $fetch_cart['price'] * $f_cart['qty'];
            $grand_total += $sub_total;
        } else {
            $warning_msg[] = 'Product not found in cart';
        }
    } else {
        $warning_msg[] = 'Product not found in cart';
    }
}

// Update the order with total products and grand total
$update_order = $conn->prepare("UPDATE orders SET total_products = ?, total_price = ? WHERE id = ?");
$update_result = $update_order->execute([$varify_cart->rowCount(), $grand_total, $id]);

// ... (rest of the code)

    
            if (!$update_result) {
                $error_info = $update_order->errorInfo();
                error_log("Failed to update order details: " . implode(" | ", $error_info));
                $warning_msg[] = 'Failed to update order details';
            }
    
            // Delete items from the cart
            $delete_cart_id = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $result_cart = $delete_cart_id->execute([$user_id]);
    
            if (!$result_cart) {
                $error_info = $delete_cart_id->errorInfo();
                error_log("Failed to delete cart items: " . implode(" | ", $error_info));
                $warning_msg[] = 'Failed to delete cart items';
            } else {
                header('location: order.php');
            }
        } else {
            $error_info = $insert_order->errorInfo();
            error_log("Failed to insert order: " . implode(" | ", $error_info));
            $warning_msg[] = 'Failed to place the order';
        }
    } else {
        $warning_msg[] = 'Cart is empty';
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
    <title> Green Coffee - Checkout page </title>
</head>
<body>
    <?php include '../components/user_header.php'; ?>
    <div class = "main">
        <div class = "banner">
            <h1> Checkout summary </h1>
        </div>
        <div class = "title2">
            <a href = "home.php"> home </a><span>/  Checkout summary </span>
        </div>
        <section class="checkout">
            <div class = "title">
                <img src = "../img/download.png" class = "logo">
                <h1>Checkout Summary</h1>

            </div>       
                <div class = "row">
                    <form method = "post">
                        <h3>billing details</h3>
                        <div class = "flex">
                            <div class = "box">
                                <div class = "input-field">
                                    <p>your name<span>*</span></p>
                                    <input type = "text" name = "name" required maxlength = "50" placeholder 
                                     = "Enter your name" class = "input">
                                </div>
                                <div class = "input-field">
                                    <p>your number<span>*</span></p>
                                    <input type = "number" name = "number" required maxlength = "10" placeholder 
                                     = "Enter your number" class = "input">
                                </div>
                                <div class = "input-field">
                                    <p>your email<span>*</span></p>
                                    <input type = "email" name = "email" required maxlength = "50" placeholder 
                                     = "Enter your email" class = "input">
                                </div>
                                <div class = "input-field">
                                    <p>payment method<span>*</span></p>
                                    <select name = "method" class = "input">
                                        <option value = "cash on delivery">cash on delivery</option>
                                        <option value = "credit or debit card">credit or debit card</option>
                                        <option value = "net banking">net banking</option>
                                        <option value = "UBI or RuPay">UBI or RuPay</option>
                                        <option value = "paytm">paytm</option>
                                    </select>
                                </div>
                                <div class = "input-field">
                                    <p>address type<span>*</span></p>
                                    <select name = "address_type" class = "input">
                                        <option value = "Home">Home</option>
                                        <option value = "Office">Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class = "box">
                                <div class = "input-field">
                                    <p>address line 01<span>*</span></p>
                                    <input type = "text" name = "flat" required maxlength = "50" placeholder 
                                     = "e.g flat and house number" class = "input">
                                </div>
                                <div class = "input-field">
                                    <p>address line 02<span>*</span></p>
                                    <input type = "text" name = "street" required maxlength = "50" placeholder 
                                     = "street name" class = "input">
                                </div>
                                <div class = "input-field">
                                    <p>city name<span>*</span></p>
                                    <input type = "text" name = "city" required maxlength = "50" placeholder 
                                     = "your city" class = "input">
                                </div>
                                <div class = "input-field">
                                    <p>country name<span>*</span></p>
                                    <input type = "text" name = "country" required maxlength = "50" placeholder 
                                     = "your country" class = "input">
                                </div>
                                <div class = "input-field">
                                    <p>pincode<span>*</span></p>
                                    <input type = "text" name = "pincode" required maxlength = "50" placeholder 
                                     = "110022" min = "0" max = "999999" class = "input">
                                </div>
                            </div>    
                        </div>
                        <button type = "submit" name = "place_order" class = "btn">place order </button>
                    </form>
                    <div class = "summary" >
                    <h3>my bag</h3>
                    <div class = "box-container" >
                        <?php
                            $grand_total = 0;
                            if (isset($_GET['get_id'])) {
                                $select_get = $conn->prepare("SELECT * FROM products WHERE id =?" );
                                $select_get->execute([$_GET['get_id']]);
                                while($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)){
                                    $sub_total = $fetch_get['price'];
                                    $grand_total+=$sub_total;
                        ?>
                        <div class = "flex">
                            <img src = "../image/<?=$fetch_get['image']; ?>" class = "image">
                            <div>
                                <h3 class = "name"><?=$fetch_get['name']; ?></h3>
                                <p class = "price"><?=$fetch_get['price']; ?>/-</p>
                            </div>
                        </div>
                        <?php            
                                }
                            }else{
                                $select_cart = $conn->prepare("SELECT * FROM cart WHERE user_id =?" );
                                $select_cart->execute([$user_id]);
                                if ($select_cart->rowCount()>0){
                                    while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                                        $select_products = $conn->prepare("SELECT * FROM products WHERE id =?" );
                                        $select_products->execute([$fetch_cart['product_id']]);
                                        $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
                                        $sub_total = ($fetch_cart['qty'] * $fetch_product['price']);
                                        $grand_total += $sub_total;
                        ?>
                        <div class = "flex">
                            <img src = "../image/<?=$fetch_product['image']; ?>" >
                            <div>
                                <h3 class="name"><?=$fetch_product['name']; ?></h3>
                                <p class = "price"><?=$fetch_product['price']; ?> X <?=$fetch_cart['qty']; ?></p>
                            </div>
                        </div>
                        <?php                
                                    }
                                }else '<p class = "empty">your cart is empty</p>';
                            }
                        ?>
                    </div>
                    <div class = "grand-total"><span>total amount payable: </span><?= $grand_total ?>Rs/-</div>
                    </div>
                </div> 
        </section>
        <?php include '../components/user_footer.php'; ?>
    </div>
    <script scr ="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script scr ="script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>
</html>