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

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total_products
     FROM products
     WHERE farmer_id = ?"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$total_products =
    $result->fetch_assoc()["total_products"];

?>

<!DOCTYPE html>
<html>

<head>

    <title>Farmer Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">



</head>

<body>

<h1>
    Welcome Farmer,
    <?php echo htmlspecialchars($_SESSION["name"]); ?>
</h1>

<h2>
    Total Products:
    <?php echo $total_products; ?>
</h2>

<br>

<a href="add_product.php">
    Add Product
</a>

<br><br>

<a href="my_products.php">
    My Products
</a>

<br><br>

<a href="farmer_orders.php">
    Customer Orders
</a>

<br><br>

<a href="../logout.php">
    Logout
</a>

</body>

</html>