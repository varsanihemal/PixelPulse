<?php
session_start();

require ('includes/connect.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $errors[] = "Both email and password are required.";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE email = :email";
        $statement = $db->prepare($query);
        $statement->bindParam(':email', $email);
        $statement->execute();

        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set success message
            $_SESSION['success_message'] = "Login successful!";

            // Set user session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];

            // Set admin status
            $_SESSION['is_admin'] = $user['is_admin']; // Assuming 'is_admin' is a boolean value in the database

            // Redirect to index.php
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Invalid email or password";
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
    <?php if (isset($_SESSION['logout_success']) && $_SESSION['logout_success']): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['logout_success']; ?>
        </div>
        <?php unset($_SESSION['logout_success']); ?> <!-- Unset the logout success message -->
    <?php endif; ?>
    <?php
    include ('includes/nav.php');
    ?>
    <div class="container mt-5">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p>
                        <?= $error ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="exampleInputEmail1"
                    aria-describedby="emailHelp" placeholder="Enter email">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="exampleInputPassword1"
                    placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>