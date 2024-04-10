<?php
session_start();
require ('includes/connect.php');

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get the game ID from the URL
if (isset($_GET['id'])) {
    $gamePageId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Check if the game ID is valid
    if ($gamePageId === false || $gamePageId === null || $gamePageId <= 0) {
        header("Location: index.php");
        exit;
    }

    // Fetch the game details from the database
    $query = "SELECT * FROM games WHERE game_id = :id";
    $statement = $db->prepare($query);
    $statement->bindParam(':id', $gamePageId, PDO::PARAM_INT);
    $statement->execute();
    $fullgame = $statement->fetch();

    // Check if the game exists
    if (!$fullgame) {
        header("Location: index.php");
        exit;
    }
} else {
    // Redirect if the game ID is not provided
    header("Location: index.php");
    exit;
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $name = isset($_POST['name']) ? trim($_POST['name']) : ''; // Handle if 'name' is not set
    $content = trim($_POST['content']);



    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Insert the comment into the database
        $query = "INSERT INTO comments (game_id, user_id, content, created_at) VALUES (:game_id, :user_id, :content, NOW())";
        $statement = $db->prepare($query);
        $statement->bindParam(':game_id', $gamePageId, PDO::PARAM_INT);

        // Check if the user is logged in
        if ($isLoggedIn) {
            $userId = $_SESSION['user_id'];
        } else {
            // If the user is not logged in, set user_id to a default value (e.g., 0)
            $userId = 0;
        }
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);

        $statement->bindParam(':content', $content, PDO::PARAM_STR);
        $statement->execute();

        // Redirect back to the game page after submitting the comment
        header("Location: gamepage.php?id=$gamePageId");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Comment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <?php include ('includes/nav.php'); ?>

    <div class="container">
        <h2>Add Comment for <?= $fullgame['title']; ?></h2>
        <?php if (isset($captcha_error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $captcha_error; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="comment.php?id=<?= $gamePageId ?>">
            <?php if (!$isLoggedIn): ?>
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="content" class="form-label">Comment</label>
                <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
            </div>
            <!-- CAPTCHA -->
            <!-- <div class="mb-3">
                <label for="captcha" class="form-label">CAPTCHA</label>
                <input type="text" class="form-control" id="captcha" name="captcha" required>
                <img src="captcha.php" alt="">
            </div> -->
            <!-- End CAPTCHA -->
            <button type="submit" name="submit_comment" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>