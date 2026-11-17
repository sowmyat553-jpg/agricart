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
    "SELECT * FROM products
     WHERE farmer_id = ?
     ORDER BY id DESC"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$products = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Products - AgriCart</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<h2>🌾 My Products</h2>

<p>
    <a href="farmer_index.php">
        Dashboard
    </a>
</p>

<p>
    <a href="add_product.php">
        Add New Product
    </a>
</p>

<hr>

<?php if ($products->num_rows > 0): ?>

    <?php while ($product = $products->fetch_assoc()): ?>

        <div>

            <?php if (!empty($product["image"])): ?>

                <img
                    src="../uploads/<?php
                    echo htmlspecialchars(
                        $product["image"]
                    );
                    ?>"
                    width="150"
                    height="120"
                >

            <?php endif; ?>

            <h3>
                <?php
                echo htmlspecialchars(
                    $product["name"]
                );
                ?>
            </h3>

            <p>
                <?php
                echo htmlspecialchars(
                    $product["description"]
                );
                ?>
            </p>

            <p>
                Price:
                ₹<?php
                echo number_format(
                    $product["price"],
                    2
                );
                ?>
            </p>

            <p>
                Quantity:
                <?php echo $product["quantity"]; ?>
            </p>

            <a
                href="edit_product.php?id=<?php
                echo $product["id"];
                ?>"
            >
                Edit
            </a>

            |

            <a
                href="delete_product.php?id=<?php
                echo $product["id"];
                ?>"
                onclick="return confirm(
                    'Are you sure you want to delete this product?'
                )"
            >
                Delete
            </a>

        </div>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>
        You have not added any products yet.
    </p>

<?php endif; ?>

<br>

<a href="../logout.php">
    Logout
</a>

</body>

</html>