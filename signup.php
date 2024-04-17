<?php
require ('includes/connect.php');

$errors = []; // errors to be stored in this varaible

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // validate username
    if (empty($_POST['username'])) {
        $errors[] = "Username is required.";
    } else {
        $username = $_POST['username'];
        // You can add more username validation rules here if needed
    }

    // validate email
    if (empty($_POST['email'])) {
        $errors[] = "Email is required.";
    } else {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
    }
    // Validate Password
    if (empty($_POST['password'])) {
        $errors[] = "Password is required.";
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Passwords dont match";
    } else {
        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    // if no errors register user
    if (empty($errors)) {
        $query = "INSERT INTO users (email, username, password) VALUES (:email,:username ,:password)";
        $statement = $db->prepare($query);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->bindParam(':password', $hashedPassword);

        if ($statement->execute()) {
            header("Location: login.php");
        } else {
            $errors[] = "Failed to register user.";
        }

    }

}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php
    include ('includes/nav.php');
    ?>

    <div class="container mt-5">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?= $error ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="inputEmail" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="inputEmail" aria-describedby="emailHelp"
                    placeholder="Enter email">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                    else.</small>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Enter username">
            </div>
            <div class="mb-3">
                <label for="inputPassword" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
            </div>
            <div class="mb-3">
                <label for="inputPasswordAgain" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" id="inputPasswordAgain"
                    placeholder="Confirm Password">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>