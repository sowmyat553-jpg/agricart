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

$product_id = intval($_POST["product_id"] ?? 0);

$quantity = intval($_POST["quantity"] ?? 1);

if ($quantity < 1) {
    $quantity = 1;
}

$stmt = $conn->prepare(
    "SELECT * FROM products
     WHERE id = ? AND quantity > 0"
);

$stmt->bind_param("i", $product_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Product not available.");
}

$product = $result->fetch_assoc();

if ($quantity > $product["quantity"]) {
    die("Requested quantity is not available.");
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$found = false;

foreach ($_SESSION["cart"] as &$item) {

    if ($item["product_id"] == $product_id) {

        $item["quantity"] += $quantity;

        if ($item["quantity"] > $product["quantity"]) {
            $item["quantity"] = $product["quantity"];
        }

        $found = true;

        break;
    }
}

unset($item);

if (!$found) {

    $_SESSION["cart"][] = [

        "product_id" => $product["id"],

        "name" => $product["name"],

        "price" => $product["price"],

        "quantity" => $quantity,

        "image" => $product["image"]

    ];
}

header("Location: cart.php");

exit();

?>