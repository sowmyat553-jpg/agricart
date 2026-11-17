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

$id = intval($_GET["id"] ?? 0);

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "DELETE FROM products
     WHERE id = ? AND farmer_id = ?"
);

$stmt->bind_param(
    "ii",
    $id,
    $user_id
);

$stmt->execute();

header("Location: my_products.php");

exit();

?>