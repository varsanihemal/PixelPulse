<?php
session_start();
include ('./includes/connect.php');

$query = "SELECT * FROM games";
$statement = $db->query($query);
$games = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_game'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Validate form data
    if (empty($title) || empty($description) || empty($price) || empty($category_id)) {
        $error_message = "All fields are required.";
    } else {
        // Insert the game details into the database
        $query = "INSERT INTO games (category_id, title, description, price) VALUES (:category_id, :title, :description, :price)";
        $statement = $db->prepare($query);
        $statement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':description', $description, PDO::PARAM_STR);
        $statement->bindParam(':price', $price, PDO::PARAM_STR);

        if ($statement->execute()) {
            header("Location: allgames.php");
            exit;
        } else {
            $error_message = "Failed to add the game. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Games</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include ('./includes/nav.php'); ?>
    <div class="container">
        <h2>Add Game</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $error_message ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category_id" required>
                    <option value="">Select a category</option>
                    <option value="1">Action</option>
                    <option value="2">Adventure</option>
                    <option value="3">Sports/Racing</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="add_game">Add Game</button>
        </form>
        <h2>Existing Games</h2>
        <div class="row">
            <?php foreach ($games as $game): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= $game['title'] ?></h5>
                            <!-- <p class="card-text"><?= $game['description'] ?></p> -->
                            <p class="card-text">Price: $<?= $game['price'] ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Edit -->
                                <a href="editgame.php?id=<?= $game['game_id'] ?>" class="btn btn-primary">Edit</a>
                                <!-- Delete -->
                                <form method="post" action="delete_game.php">
                                    <input type="hidden" name="game_id" value="<?= $game['game_id'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>