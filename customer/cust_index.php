<?php

session_start();

include "../db.php";

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "customer"
) {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Customer Dashboard - AgriCart</title>
    <link rel="stylesheet" href="../css/style.css">



</head>

<body>

<h1>
    🌾 Welcome,
    <?php
    echo htmlspecialchars($_SESSION["name"]);
    ?>
</h1>

<p>
    Buy fresh agricultural products directly
    from farmers.
</p>

<hr>

<h2>Customer Dashboard</h2>

<p>
    <a href="browse_products.php">
        🛒 Browse Products
    </a>
</p>

<p>
    <a href="cart.php">
        🛍️ My Cart
    </a>
</p>

<p>
    <a href="my_orders.php">
        📦 My Orders
    </a>
</p>

<p>
    <a href="../logout.php">
        Logout
    </a>
</p>

</body>

</html>