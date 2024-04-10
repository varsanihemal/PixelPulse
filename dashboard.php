<?php
session_start();
include ('functions.php');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        addUser($_POST['email'], $_POST['password']);
    } elseif (isset($_POST['update_user'])) {
        updateUser($_POST['user_id'], $_POST['email']);
    } elseif (isset($_POST['delete_user'])) {
        deleteUser($_POST['user_id']);
    }
}

$users = getAllUsers();
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
    <link rel="stylesheet" href="./includes/dashboard.css">
</head>

<body>
    <?php
    include ('./includes/nav.php')
        ?>
    <?php
    $users = getAllUsers();
    var_dump($users); // Add this line to inspect the structure of the $users array
    ?>
    <div class="container">
        <h1>Manage Users</h1>
        <h2>Add New User</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
        </form>
        <h2>All Users</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Update Or Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['user_id'] ?></td>
                        <td>
                            <!-- Check if 'email' key exists and is not empty before displaying -->
                            <?php if (isset($user['email']) && !empty($user['email'])): ?>
                                <?= $user['email'] ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Add buttons for update and delete actions -->
                            <form action="" method="post">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <!-- Add input field for updating email -->
                                <input type="text" name="email" value="<?= $user['email'] ?>">
                                <button type="submit" name="update_user" class="btn btn-primary">Update</button>
                                <button type="submit" name="delete_user" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>


        </table>
        <?php if (isset($_SESSION['user_deleted'])): ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['user_deleted'] ?>
            </div>
            <?php unset($_SESSION['user_deleted']); ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>