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

$sql = "
SELECT
    products.*,
    users.name AS farmer_name
FROM products
JOIN users
ON products.farmer_id = users.id
WHERE products.quantity > 0
ORDER BY products.id DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Browse Products - AgriCart</title>
    <link rel="stylesheet" href="../css/style.css">
    

</head>

<body>

<h1>🌾 Agricultural Products</h1>

<p>

    <a href="cust_index.php">
        Dashboard
    </a>

    |

    <a href="cart.php">
        🛒 My Cart
    </a>

    |

    <a href="my_orders.php">
        📦 My Orders
    </a>

</p>

<hr>

<?php if ($result->num_rows > 0): ?>

    <?php while ($product = $result->fetch_assoc()): ?>

        <div>

            <?php if (!empty($product["image"])): ?>

                <img
                    src="../uploads/<?php
                    echo htmlspecialchars(
                        $product["image"]
                    );
                    ?>"
                    width="200"
                    height="150"
                >

                <br>

            <?php endif; ?>

            <h2>
                <?php
                echo htmlspecialchars(
                    $product["name"]
                );
                ?>
            </h2>

            <p>
                <?php
                echo htmlspecialchars(
                    $product["description"]
                );
                ?>
            </p>

            <p>
                Farmer:
                <strong>
                    <?php
                    echo htmlspecialchars(
                        $product["farmer_name"]
                    );
                    ?>
                </strong>
            </p>

            <p>
                Price:
                <strong>
                    ₹<?php
                    echo number_format(
                        $product["price"],
                        2
                    );
                    ?>
                </strong>
            </p>

            <p>
                Available:
                <?php echo $product["quantity"]; ?>
            </p>

            <form
                method="POST"
                action="buy_product.php"
            >

                <input
                    type="hidden"
                    name="product_id"
                    value="<?php
                    echo $product["id"];
                    ?>"
                >

                <label>Quantity:</label>

                <input
                    type="number"
                    name="quantity"
                    value="1"
                    min="1"
                    max="<?php
                    echo $product["quantity"];
                    ?>"
                    required
                >

                <button type="submit">
                    Add to Cart
                </button>

            </form>

        </div>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>
        No products available at the moment.
    </p>

<?php endif; ?>

<br>

<a href="../logout.php">
    Logout
</a>

</body>

</html>