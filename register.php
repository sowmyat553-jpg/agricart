<?php

include "db.php";

$message = "";

if (isset($_POST["register"])) {

    $name = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (
        $name == "" ||
        $username == "" ||
        $email == "" ||
        $password == "" ||
        $role == ""
    ) {

        $message = "Please fill all fields.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE username = ?"
        );

        $check->bind_param("s", $username);

        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Username already exists.";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, username, email, password, role)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $name,
                $username,
                $email,
                $hashed_password,
                $role
            );

            if ($stmt->execute()) {

                header("Location: login.php?registered=1");
                exit();

            } else {

                $message = "Registration failed.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Register - AgriCart</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
<div class="login-box">
<h2>🌾 AgriCart Registration</h2>

<?php if ($message != ""): ?>

    <p>
        <?php echo htmlspecialchars($message); ?>
    </p>

<?php endif; ?>

<form method="POST">

    <label>Full Name</label><br>

    <input
        type="text"
        name="name"
        required
    >

    <br><br>

    <label>Username</label><br>

    <input
        type="text"
        name="username"
        required
    >

    <br><br>

    <label>Email</label><br>

    <input
        type="email"
        name="email"
        required
    >

    <br><br>

    <label>Password</label><br>

    <input
        type="password"
        name="password"
        required
    >

    <br><br>

    <label>Select Role</label><br>

    <select name="role" required>

        <option value="">
            Select Role
        </option>

        <option value="farmer">
            Farmer
        </option>

        <option value="customer">
            Customer
        </option>

    </select>

    <br><br>

    <button
        type="submit"
        name="register"
    >
        Register
    </button>

</form>

<br>

<a href="login.php">
    Already have an account? Login
</a>
</div>

</body>

</html>