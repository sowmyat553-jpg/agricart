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

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT *
     FROM orders
     WHERE user_id = ?
     ORDER BY id DESC"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$orders = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>My Orders - AgriCart</title>
<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<h1>📦 My Orders</h1>

<p>

<a href="cust_index.php">Dashboard</a> |

<a href="browse_products.php">Browse Products</a> |

<a href="cart.php">My Cart</a>

</p>

<hr>

<?php if (isset($_GET["success"])): ?>

<p>
<strong>✅ Order placed successfully!</strong>
</p>

<?php endif; ?>


<?php if ($orders->num_rows > 0): ?>

<table border="1" cellpadding="10">

<tr>

<th>Order ID</th>

<th>Date</th>

<th>Total Price</th>

<th>Status</th>

</tr>


<?php while ($order = $orders->fetch_assoc()): ?>

<tr>

<td>
#<?php echo $order["id"]; ?>
</td>

<td>

<?php

echo date(
    "d-m-Y H:i",
    strtotime($order["order_date"])
);

?>

</td>

<td>

₹<?php

echo number_format(
    $order["total_price"],
    2
);

?>

</td>

<td>

<?php

echo htmlspecialchars(
    $order["status"]
);

?>

</td>

</tr>

<?php endwhile; ?>


</table>

<?php else: ?>

<p>No orders found.</p>

<?php endif; ?>


<br>

<a href="../logout.php">Logout</a>

</body>

</html>