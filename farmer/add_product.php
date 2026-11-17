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

$message = "";

if (isset($_POST["add_product"])) {

    $farmer_id = $_SESSION["user_id"];

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $quantity = intval($_POST["quantity"]);

    $image_name = "";

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] == 0
    ) {

        $extension = strtolower(
            pathinfo(
                $_FILES["image"]["name"],
                PATHINFO_EXTENSION
            )
        );

        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (in_array($extension, $allowed)) {

            $image_name =
                time() . "_" .
                basename($_FILES["image"]["name"]);

            move_uploaded_file(
                $_FILES["image"]["tmp_name"],
                "../uploads/" . $image_name
            );
        }
    }

    $stmt = $conn->prepare(
        "INSERT INTO products
        (farmer_id, name, description, price, quantity, image)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "issdis",
        $farmer_id,
        $name,
        $description,
        $price,
        $quantity,
        $image_name
    );

    if ($stmt->execute()) {

        $message = "Product added successfully.";

    } else {

        $message = "Failed to add product.";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Product - AgriCart</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<h2>🌾 Add Agricultural Product</h2>

<?php if ($message != ""): ?>

    <p>
        <?php echo htmlspecialchars($message); ?>
    </p>

<?php endif; ?>

<form
    method="POST"
    enctype="multipart/form-data"
>

    <label>Product Name</label>

    <br>

    <input
        type="text"
        name="name"
        required
    >

    <br><br>

    <label>Description</label>

    <br>

    <textarea
        name="description"
        required
    ></textarea>

    <br><br>

    <label>Price</label>

    <br>

    <input
        type="number"
        name="price"
        step="0.01"
        min="0"
        required
    >

    <br><br>

    <label>Quantity</label>

    <br>

    <input
        type="number"
        name="quantity"
        min="1"
        required
    >

    <br><br>

    <label>Product Image</label>

    <br>

    <input
        type="file"
        name="image"
        accept="image/*"
    >

    <br><br>

    <button
        type="submit"
        name="add_product"
    >
        Add Product
    </button>

</form>

<br>

<a href="farmer_index.php">
    Back to Dashboard
</a>

</body>

</html>