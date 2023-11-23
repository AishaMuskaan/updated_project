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

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location: order.php');
}

if (isset($_POST['cancel'])) {
    $update_order = $conn->prepare("UPDATE `order` SET status = ? WHERE id = ?");
    $update_order->execute(['canceled', $get_id]);
    header('location: order.php');
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
    <title> Green Coffee - order detail </title>
</head>

<body>
    <?php include '../components/user_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1> order detail </h1>
        </div>
        <div class="title2">
            <a href="home.php"> home </a><span>/ order detail </span>
        </div>
        <section class="order details">
            <div class="title">
                <img src="../img/download.png" class="logo">
                <h1>order detail</h1>
                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Suscipit tenetur impedit voluptatibus
                    aperiam quibusdam adipisci quo praesentium explicabo, recusandae maxime natus</p>
            </div>
            <div class="box-container">
                <?php
                $grand_total = 0;
                $select_orders = $conn->prepare("SELECT * FROM orders WHERE id=? LIMIT 1");
                $select_orders->execute([$get_id]);
                if ($select_orders->rowCount() > 0) {
                    while ($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        $select_order_items = $conn->prepare("SELECT * FROM `order-items` WHERE order_id=?");
                        $select_order_items->execute([$get_id]);

                        while ($order_item = $select_order_items->fetch(PDO::FETCH_ASSOC)) {
                            $select_product = $conn->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
                            $select_product->execute([$order_item['product_id']]);
                            if ($select_product->rowCount() > 0) {
                                $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
                                $sub_total = ($fetch_product['price'] * $order_item['quantity']);
                                $grand_total += $sub_total;
                ?>
                                <div class="box">
                                    <div class="col">
                                        <p class="tltle"><i class="bi bi-calendar-fill"></i><?= $fetch_order['date']; ?></p>
                                        <img src="../image/<?= $fetch_product['image']; ?>" class="image">
                                        <p class="price"><?= $fetch_product['price']; ?> x <?= $order_item['quantity']; ?></p>
                                        <h3 class="name"><?= $fetch_product['name']; ?></h3>
                                        <p class="grand-total">Total amount payable : <span><?= $grand_total; ?>Rs</span></p>
                                    </div>
                                    <div class = "col">
                        <p class = "title">billing address </p>
                        <p class = "user"><i class = "bi bi-person-bounding-box"></i><?= $fetch_product['name']; ?></p>
                        <p class = "user"><i class = "bi bi-phone"></i><?= $fetch_order['number']; ?></p>
                        <p class = "user"><i class = "bi bi-envelope"></i><?= $fetch_order['email']; ?></p>
                        <p class = "user"><i class = "bi bi-pin-map-fill"></i><?= $fetch_order['address']; ?></p>
                        <p class = "title">status</p>
                        <p class = "status" style = "color:<?php if ($fetch_order['status'] == 'delevered'){
                            echo'green';}elseif($fetch_order['status'] == 'canceled'){echo'red';}else{echo'orange';}?>">
                            <?=$fetch_order['status'] ?></p>
                        <?php if ($fetch_order['status'] == 'canceled') {?>
                            <a href = "checkout.php?get_id = <?= $fetch_product['id']; ?>" class = "btn">order again</a>
                        <?php  }else{ ?>
                            <form method = "post">
                                <button type = "submit" name = "cancel" class = "btn" onclick = "return confirm
                                ('do you want to cancel this order')">cancel order</button>
                            </form>
                        <?php } ?>        
                    </div>
                                </div>
                <?php
                            } else {
                                echo '<p class="empty">Product not found</p>';
                            }
                        }
                    }
                } else {
                    echo '<p class="empty">No order found</p>';
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
