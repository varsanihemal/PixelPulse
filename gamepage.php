<?php
session_start();
require ('includes/connect.php');

// Check if the user is logged in and if they are an admin
$isLoggedIn = isset($_SESSION['user_id']);

if (isset($_GET['id'])) {
    $gamePageId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($gamePageId === false || $gamePageId === null || $gamePageId <= 0) {
        header("Location: index.php");
        exit;
    }

    $query = "SELECT * FROM games WHERE game_id = :id";
    $statement = $db->prepare($query);
    $statement->bindParam(':id', $gamePageId, PDO::PARAM_INT);

    $statement->execute();

    $fullgame = $statement->fetch();

    if (!$fullgame) {
        header("Location: index.php");
        exit;
    }
    // Fetch comments for the game from the database
    $query = "SELECT * FROM comments WHERE game_id = :game_id ORDER BY created_at DESC";
    $statement = $db->prepare($query);
    $statement->bindParam(':game_id', $gamePageId, PDO::PARAM_INT);
    $statement->execute();
    $comments = $statement->fetchAll();
} else {
    header("Location: index.php");
    exit;
}


?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="./utilities/fullgame.css">
</head>
</head>

<body>

    <?php
    include ('includes/nav.php');
    ?>

    <div class="container">
        <div class="fullpage-container">
            <div class="banner">
                <?php if (!empty($fullgame['cover_image_path'])): ?>
                    <img src="<?= $fullgame['cover_image_path'] ?>" alt="">
                <?php endif; ?>
            </div>
            <div class="content">
                <h2>
                    <?= $fullgame['title'] ?>
                </h2>
                <p>
                    <?= $fullgame['description'] ?>
                </p>
                <p>
                    <?= $fullgame['price'] ?>
                </p>
                <p>
                    <?= $fullgame['release_date'] ?>
                </p>
                <?php if ($isLoggedIn): ?>
                    <a href="editgame.php?id=<?= $gamePageId ?>" class="btn btn-primary">Edit</a>
                    <?php endif; ?>
                    <a href="comment.php?id=<?= $gamePageId ?>" class="btn btn-primary">Add Comment</a>
            </div>
        </div>
        <!-- Comment -->
        <?php if (!empty($comments)): ?>
            <h3>Comments</h3>
            <ul>
                <?php foreach ($comments as $comment): ?>
                    <li>
                        <?= $comment['content'] ?>
                        <p>Created at:
                            <?= $comment['created_at'] ?>
                        </p>
                        <?php if ($comment['updated_at'] !== null): ?>
                            <p>Updated at:
                                <?= $comment['updated_at'] ?>
                            </p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>