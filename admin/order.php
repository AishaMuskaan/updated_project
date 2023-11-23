<?php
include '../components/connection.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location: login.php');
}

// update order
if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['update_payment'];

    $update_order = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_order->execute([$status, $order_id]);

    if ($update_order) {
        $success_msg[] = 'Order status updated';
    } else {
        $warning_msg[] = 'Failed to update order status';
    }
}

// delete order
if(isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    $order_id = filter_var($order_id, FILTER_SANITIZE_STRING);
    
    $verify_delete = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $verify_delete->execute([$order_id]);

    if($verify_delete->rowCount()>0){
        $delete_order = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $delete_order->execute([$order_id]);
        $success_msg[] = 'Order deleted';
    }else{
        $warning_msg[] = 'Order already deleted';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- boxicon cdn link -->
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="admin_style.css?v=<?php echo time(); ?>">
    <title>Admin Panel - Order Placed Page</title>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Order Placed</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Dashboard</a><span> / Order Placed</span>
        </div>
        <section class="order-container">
            <h1 class="heading">Total Orders Placed</h1>
            <div class="box-container">
                <?php
                $select_orders = $conn->prepare("SELECT * FROM orders");
                $select_orders->execute();

                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <div class="box">
                            <div class="status" style="color: <?php if($fetch_orders['status'] == 'in progress'
                            ){echo "green";}else{echo "red";} ?>"><?= $fetch_orders['status']; ?></div>
                            <div class="detail">
                                <p>User Name: <span><?= $fetch_orders['name']; ?></span></p>
                                <p>User ID: <span><?= $fetch_orders['user_id']; ?></span></p>
                                <p>Placed On: <span><?= $fetch_orders['date']; ?></span></p>
                                <p>User Number: <span><?= $fetch_orders['number']; ?></span></p>
                                <p>User Email: <span><?= $fetch_orders['email']; ?></span></p>
                                <p>Total Price: <span>$<?= $fetch_orders['total_price']; ?></span></p>
                                <p>Method: <span><?= $fetch_orders['method']; ?></span></p>
                                <p>Address: <span><?= $fetch_orders['address']; ?></span></p>
                            </div>
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
                                <p>Price: <span>$<?= $product['price']; ?></span></p>
                               
                                <?php
                            }
                            ?>
                            <form action="" method="post">
                                <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>" readonly>
                                <select name="update_payment">
                                    <option disabled selected><?= $fetch_orders['status']; ?></option>
                                    <option value="pending">Pending</option>
                                    <option value="complete">Complete</option>
                                    <option value="in progress">In Progress</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                                <div class="flex-btn">
                                    <button type="submit" name="update_order" class="btn">Update Order</button>
                                    <button type="submit" name="delete_order" class="btn">Delete Order</button>
                                </div>
                            </form>
                        </div>
                        <?php
                    }
                } else {
                    echo '
                        <div class="empty">
                            <p>No orders have been placed yet</p>
                        </div>
                    ';
                }
                ?>
            </div>
        </section>
    </div>

    <!-- sweetalert cdn link -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js link -->
    <script type="text/javascript" src="script.js"></script>

    <!-- alert -->
    <?php include '../components/alert.php'; ?>
</body>
</html>
