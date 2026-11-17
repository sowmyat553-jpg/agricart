<?php

session_start();

include "../db.php";

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "farmer"
) {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION["user_id"];

$sql = "
SELECT
    orders.id AS order_id,
    orders.order_date,
    orders.status,
    users.name AS customer_name,
    products.name AS product_name,
    order_items.quantity,
    order_items.price

FROM order_items

JOIN orders
ON order_items.order_id = orders.id

JOIN products
ON order_items.product_id = products.id

JOIN users
ON orders.user_id = users.id

WHERE products.farmer_id = ?

ORDER BY orders.id DESC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $farmer_id);

$stmt->execute();

$orders = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Farmer Orders - AgriCart</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<h2>📦 Customer Orders</h2>

<p>
    <a href="farmer_index.php">
        Dashboard
    </a>
</p>

<p>
    <a href="my_products.php">
        My Products
    </a>
</p>

<hr>

<?php if ($orders->num_rows > 0): ?>

<table border="1" cellpadding="10">

    <tr>

        <th>Order ID</th>

        <th>Customer</th>

        <th>Product</th>

        <th>Quantity</th>

        <th>Price</th>

        <th>Date</th>

        <th>Status</th>

    </tr>

    <?php while ($order = $orders->fetch_assoc()): ?>

    <tr>

        <td>
            #<?php echo $order["order_id"]; ?>
        </td>

        <td>
            <?php
            echo htmlspecialchars(
                $order["customer_name"]
            );
            ?>
        </td>

        <td>
            <?php
            echo htmlspecialchars(
                $order["product_name"]
            );
            ?>
        </td>

        <td>
            <?php echo $order["quantity"]; ?>
        </td>

        <td>
            ₹<?php
            echo number_format(
                $order["price"],
                2
            );
            ?>
        </td>

        <td>
            <?php
            echo date(
                "d-m-Y",
                strtotime(
                    $order["order_date"]
                )
            );
            ?>
        </td>

        <td>
            <?php
            echo htmlspecialchars(
                $order["status"]
            );
            ?>
        </td>

    </tr>

    <?php endwhile; ?>

</table>

<?php else: ?>

<p>
    No customer orders yet.
</p>

<?php endif; ?>

<br>

<a href="../logout.php">
    Logout
</a>

</body>

</html>