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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $name = isset($_POST['name']) ? trim($_POST['name']) : ''; // Handle if 'name' is not set
    $content = trim($_POST['content']);

    // Check CAPTCHA
    if (!isset($_SESSION['captcha']) || $_SESSION['captcha'] != $_POST['captcha']) {
        $captcha_error = "CAPTCHA code is incorrect. Please try again.";
    } else {
        // Insert the comment into the database
        $query = "INSERT INTO comments (game_id, user_id, content, created_at) VALUES (:game_id, :user_id, :content, NOW())";
        $statement = $db->prepare($query);
        $statement->bindParam(':game_id', $gamePageId, PDO::PARAM_INT);

        // Check if the user is logged in
        if ($isLoggedIn) {
            $userId = $_SESSION['user_id'];
        } else {
            // If the user is not logged in, set user_id to NULL
            $userId = null;
        }
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);

        $statement->bindParam(':content', $content, PDO::PARAM_STR);
        $statement->execute();

        // Redirect back to the game page after submitting the comment
        header("Location: gamepage.php?id=$gamePageId");
        exit;
    }
}

// Generate a random CAPTCHA code
$captcha = substr(md5(mt_rand()), 0, 6);
$_SESSION['captcha'] = $captcha;

$content = isset($_POST['content']) ? $_POST['content'] : '';

// Generate a random CAPTCHA code
$captcha = substr(md5(mt_rand()), 0, 6);
$_SESSION['captcha'] = $captcha;
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
                <textarea class="form-control" id="content" name="content" rows="3" required><?= htmlspecialchars($content) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="captcha" class="form-label">CAPTCHA</label><br>
                <input type="text" class="form-control" id="captcha" name="captcha" required>
                <img src="captcha.php" alt="CAPTCHA"><br>
            </div>
            <button type="submit" name="submit_comment" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>