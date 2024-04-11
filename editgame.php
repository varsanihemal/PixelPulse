<?php
session_start();
require ('includes/connect.php');

$isLoggedIn = isset($_SESSION['user_id']);

if (isset($_GET['id'])) {
    $gameId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($gameId === false || $gameId === null || $gameId <= 0) {
        header("Location: index.html");
        exit;
    }

    $query = "SELECT * FROM games WHERE game_id = :id";
    $statement = $db->prepare($query);
    $statement->bindParam(':id', $gameId, PDO::PARAM_INT);
    $statement->execute();

    $game = $statement->fetch();

    if (!$game) {
        header("Location: index.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_game'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];

        // Update the game details in the database
        $query = "UPDATE games SET title = :title, description = :description WHERE game_id = :id";
        $statement = $db->prepare($query);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':description', $description, PDO::PARAM_STR);
        $statement->bindParam(':id', $gameId, PDO::PARAM_INT);

        if ($statement->execute()) {
            header("Location: gamepage.php?id=$gameId");
            exit;
        }
    } elseif (isset($_POST['delete_game'])) {
        $query = "DELETE FROM games WHERE game_id = :id";
        $statement = $db->prepare($query);
        $statement->bindParam(':id', $gameId, PDO::PARAM_INT);

        if ($statement->execute()) {
            // Redirect user to index.php
            header("Location: allgames.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <script src="tinymce/js/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: 'textarea#description',
            height: 400,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help'
        });
    </script>
</head>

<body>
    <?php include ('includes/nav.php'); ?>
    <div class="container">
        <h2>Edit Game</h2>
        <form method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= $game['title'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <!-- Sanitization -->
                <textarea id="description" name="description"><?= htmlspecialchars($game['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <textarea class="form-control" id="price" name="price" rows="3"
                    required><?= $game['price'] ?></textarea>
            </div>
            <?php if ($isLoggedIn): ?>
                <!-- Display edit and delete buttons for logged-in users -->
                <button type="submit" class="btn btn-primary" name="edit_game">Edit Game</button>
                <form method="post" action="delete_game.php">
                    <input type="hidden" name="game_id" value="<?= $game['game_id'] ?>">
                    <button type="submit" class="btn btn-danger" name="delete_game">Delete Game</button>
                </form>

            <?php endif; ?>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>