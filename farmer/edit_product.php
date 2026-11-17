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

$id = intval($_GET["id"] ?? 0);

$stmt = $conn->prepare(
    "SELECT * FROM products
     WHERE id = ? AND farmer_id = ?"
);

$stmt->bind_param("ii", $id, $user_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

$message = "";

if (isset($_POST["update"])) {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $quantity = intval($_POST["quantity"]);

    $stmt = $conn->prepare(
        "UPDATE products
         SET name = ?,
             description = ?,
             price = ?,
             quantity = ?
         WHERE id = ? AND farmer_id = ?"
    );

    $stmt->bind_param(
        "ssdiii",
        $name,
        $description,
        $price,
        $quantity,
        $id,
        $user_id
    );

    if ($stmt->execute()) {

        header("Location: my_products.php");
        exit();

    } else {

        $message = "Product update failed.";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Product - AgriCart</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<h2>✏️ Edit Product</h2>

<?php if ($message != ""): ?>

    <p>
        <?php echo htmlspecialchars($message); ?>
    </p>

<?php endif; ?>

<form method="POST">

    <label>Product Name</label>

    <br>

    <input
        type="text"
        name="name"
        value="<?php
        echo htmlspecialchars($product["name"]);
        ?>"
        required
    >

    <br><br>

    <label>Description</label>

    <br>

    <textarea
        name="description"
        required
    ><?php
    echo htmlspecialchars(
        $product["description"]
    );
    ?></textarea>

    <br><br>

    <label>Price</label>

    <br>

    <input
        type="number"
        name="price"
        step="0.01"
        min="0"
        value="<?php
        echo $product["price"];
        ?>"
        required
    >

    <br><br>

    <label>Quantity</label>

    <br>

    <input
        type="number"
        name="quantity"
        min="0"
        value="<?php
        echo $product["quantity"];
        ?>"
        required
    >

    <br><br>

    <button
        type="submit"
        name="update"
    >
        Update Product
    </button>

</form>

<br>

<a href="my_products.php">
    Back to My Products
</a>

</body>

</html>