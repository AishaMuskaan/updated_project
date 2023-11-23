<?php
include '../components/connection.php';
session_start();
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if(isset($_POST['logout'])) {
    session_destroy();
    header('location: login.php');
}
?>
<style type="text/css">
    <?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta charset="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Green Coffee - Order Page</title>
</head>
<body>
<?php include '../components/user_header.php'; ?>
<div class="main">
    <div class="banner">
        <h1>My Order</h1>
    </div>
    <div class="title2">
        <a href="home.php">Home</a><span>/ Order</span>
    </div>
    <section class="orders">
        <div class="title">
            <img src="../img/download.png" class="logo">
            <h1>My Orders</h1>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Suscipit tenetur impedit voluptatibus
                aperiam quibusdam adipisci quo praesentium explicabo, recusandae maxime natus.
            </p>
        </div>
        <div class="box-container">
        <?php
                $select_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
                $select_orders->execute([$user_id]);

                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <div class="box" style="border: 2px solid <?php echo ($fetch_orders['status'] == 'canceled') ? 'red' : 'green'; ?>;">
                            <a href="view_order.php?get_id=<?= $fetch_orders['id']; ?>">
                            <div class="detail">
                                <p class="date"><i class="bi bi-calender-fill"></i><span><?=$fetch_orders['date']; ?></span></p>
                                <p>Name: <span><?= $fetch_orders['name']; ?></span></p>
                                <p>Number: <span><?= $fetch_orders['number']; ?></span></p>
                                <p>Email: <span><?= $fetch_orders['email']; ?></span></p>
                                <p>Total Price: <span><?= $fetch_orders['total_price']; ?>Rs</span></p>
                                <p>Total Products: <span><?= $fetch_orders['qty']; ?></span></p>
                                <p>Method: <span><?= $fetch_orders['method']; ?></span></p>
                                <p>Address: <span><?= $fetch_orders['address']; ?></span></p>
                            
                            <!-- Order items loop -->
                            <?php
                            $get_order_items = $conn->prepare("SELECT * FROM `order-items` WHERE order_id = ?");
                            $get_order_items->execute([$fetch_orders['id']]);
                            while ($order_item = $get_order_items->fetch(PDO::FETCH_ASSOC)) {
                                $get_product = $conn->prepare("SELECT * FROM products WHERE id = ?");
                                $get_product->execute([$order_item['product_id']]);
                                $product = $get_product->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <hr>
                                <p>Product Name: <span><?= $product['name']; ?></span></p>
                                <p>Quantity: <span><?= $order_item['quantity']; ?></span></p>
                                <p>Price: <span><?= $product['price']; ?>Rs</span></p>
                               
                                <?php
                            }
                            ?>
                            </div>
                        </div>
                        <?php
                }
            } else {
                echo '<p class="empty">No orders have been placed yet!</p>';
            }
            ?>
        </div>
    </section>
    <?php include '../components/user_footer.php'; ?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="script.js"></script>
<?php include '../components/alert.php'; ?>
</body>
</html>
