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

if (
    !isset($_POST["purchase"]) ||
    empty($_SESSION["cart"])
) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$total_price = 0;

foreach ($_SESSION["cart"] as $item) {

    $total_price +=
        $item["price"] *
        $item["quantity"];
}


/* Check stock */

foreach ($_SESSION["cart"] as $item) {

    $stmt = $conn->prepare(
        "SELECT quantity
         FROM products
         WHERE id = ?"
    );

    $stmt->bind_param(
        "i",
        $item["product_id"]
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows != 1) {
        die("Product no longer exists.");
    }

    $product = $result->fetch_assoc();

    if (
        $item["quantity"] >
        $product["quantity"]
    ) {

        die(
            "Not enough stock for " .
            htmlspecialchars($item["name"])
        );
    }
}


/* Start transaction */

$conn->begin_transaction();

try {

    /* Create order */

    $stmt = $conn->prepare(
        "INSERT INTO orders
        (user_id, total_price, status)
        VALUES (?, ?, 'Pending')"
    );

    $stmt->bind_param(
        "id",
        $user_id,
        $total_price
    );

    $stmt->execute();

    $order_id = $conn->insert_id;


    /* Add order items */

    foreach ($_SESSION["cart"] as $item) {

        $stmt = $conn->prepare(
            "INSERT INTO order_items
            (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iiid",
            $order_id,
            $item["product_id"],
            $item["quantity"],
            $item["price"]
        );

        $stmt->execute();


        /* Reduce product stock */

        $stmt = $conn->prepare(
            "UPDATE products
             SET quantity = quantity - ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ii",
            $item["quantity"],
            $item["product_id"]
        );

        $stmt->execute();
    }


    /* Save transaction */

    $conn->commit();


    /* Empty cart */

    $_SESSION["cart"] = [];


    /* Go to orders page */

    header(
        "Location: my_orders.php?success=1"
    );

    exit();

} catch (Exception $e) {

    $conn->rollback();

    die(
        "Purchase failed: " .
        htmlspecialchars($e->getMessage())
    );
}

?>