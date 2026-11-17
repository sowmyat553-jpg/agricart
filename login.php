<?php

session_start();

include "db.php";

$message = "";

if (isset($_GET["registered"])) {
    $message = "Registration successful. Please login.";
}

if (isset($_POST["login"])) {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE username = ?"
    );

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] == "farmer") {

                header("Location: farmer/farmer_index.php");

            } else {

                header("Location: customer/cust_index.php");

            }

            exit();

        } else {

            $message = "Invalid username or password.";

        }

    } else {

        $message = "Invalid username or password.";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Login - AgriCart</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
<div class="login-box">
<h2>🌾 AgriCart Login</h2>

<?php if ($message != ""): ?>

    <p>
        <?php echo htmlspecialchars($message); ?>
    </p>

<?php endif; ?>

<form method="POST">

    <label>Username</label>

    <br>

    <input
        type="text"
        name="username"
        required
    >

    <br><br>

    <label>Password</label>

    <br>

    <input
        type="password"
        name="password"
        required
    >

    <br><br>

    <button
        type="submit"
        name="login"
    >
        Login
    </button>

</form>

<br>

<p>
    Don't have an account?
    <a href="register.php">
        Register
    </a>
</p>
</div>

</body>

</html>