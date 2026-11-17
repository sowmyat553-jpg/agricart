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

$total_price = 0;

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Cart - AgriCart</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<h1>🛒 My Cart</h1>

<p>

    <a href="cust_index.php">
        Dashboard
    </a>

    |

    <a href="browse_products.php">
        Browse Products
    </a>

    |

    <a href="my_orders.php">
        My Orders
    </a>

</p>

<hr>

<?php if (
    isset($_SESSION["cart"]) &&
    !empty($_SESSION["cart"])
): ?>

    <table border="1" cellpadding="10">

        <tr>

            <th>Product</th>

            <th>Price</th>

            <th>Quantity</th>

            <th>Total</th>

        </tr>

        <?php foreach ($_SESSION["cart"] as $item): ?>

            <?php

            $item_total =
                $item["price"] *
                $item["quantity"];

            $total_price += $item_total;

            ?>

            <tr>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $item["name"]
                    );
                    ?>
                </td>

                <td>
                    ₹<?php
                    echo number_format(
                        $item["price"],
                        2
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo $item["quantity"];
                    ?>
                </td>

                <td>
                    ₹<?php
                    echo number_format(
                        $item_total,
                        2
                    );
                    ?>
                </td>

            </tr>

        <?php endforeach; ?>

        <tr>

            <td colspan="3">

                <strong>
                    Grand Total
                </strong>

            </td>

            <td>

                <strong>
                    ₹<?php
                    echo number_format(
                        $total_price,
                        2
                    );
                    ?>
                </strong>

            </td>

        </tr>

    </table>

    <br>

    <form
        method="POST"
        action="purchase.php"
    >

        <button
            type="submit"
            name="purchase"
        >
            Proceed to Purchase
        </button>

    </form>

<?php else: ?>

    <p>
        Your cart is empty.
    </p>

    <a href="browse_products.php">
        Browse Products
    </a>

<?php endif; ?>

<br><br>

<a href="../logout.php">
    Logout
</a>

</body>

</html>