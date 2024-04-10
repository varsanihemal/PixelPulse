<?php
session_start();
require ('./includes/connect.php');

// Check if the user is logged in and if they are an admin
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['is_admin']);

if (!$isAdmin) {
    // Redirect non-admin users to the homepage or another appropriate page
    header("Location: index.php");
    exit;
}

// Handle comment removal or hiding if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $commentId = $_POST['comment_id'];
    $action = $_POST['action'];

    // Perform the moderation action
    switch ($action) {
        case 'remove':
            // Delete the comment from the database
            $query = "DELETE FROM comments WHERE comment_id = :comment_id";
            break;
        case 'hide':
            // Update the moderation status to 'hidden'
            $query = "UPDATE comments SET moderation_status = 'hidden' WHERE comment_id = :comment_id";
            break;
        case 'unhide':
            // Update the moderation status to 'visible'
            $query = "UPDATE comments SET moderation_status = 'visible' WHERE comment_id = :comment_id";
            break;
        // Add more cases for other moderation actions if needed
        default:
            // Handle unexpected action
            echo "Unexpected action!";
            exit;
    }

    $statement = $db->prepare($query);
    $statement->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $statement->execute();

    // Optionally, provide feedback to the admin about the moderation action
    $_SESSION['moderation_message'] = "Comment successfully moderated.";

    // Redirect back to the game page after the action
    if (isset($_POST['game_id'])) {
        $gamePageId = $_POST['game_id'];
        header("Location: gamepage.php?id=$gamePageId");
    } else {
        // Redirect to the manage comments page if game_id is not set
        header("Location: manage_comments.php");
    }
    exit;
}

// Fetch all comments from the database
$query = "SELECT * FROM comments ORDER BY created_at DESC";
$statement = $db->prepare($query);
$statement->execute();
$comments = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <?php include ('includes/nav.php'); ?>

    <div class="container">
        <h2>Manage Comments</h2>
        <?php if (isset($_SESSION['moderation_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['moderation_message'] ?>
            </div>
            <?php unset($_SESSION['moderation_message']); ?>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= $comment['content'] ?></td>
                        <td>
                            <form method="post" action="manage_comments.php">
                                <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                <input type="hidden" name="game_id" value="<?= $comment['game_id'] ?>">
                                <!-- Add this line -->
                                <button type="submit" name="action" value="remove" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to remove this comment?')">Remove</button>
                                <?php if ($comment['moderation_status'] === 'hidden'): ?>
                                    <button type="submit" name="action" value="unhide" class="btn btn-success"
                                        onclick="return confirm('Are you sure you want to unhide this comment?')">Unhide</button>
                                <?php else: ?>
                                    <button type="submit" name="action" value="hide" class="btn btn-warning"
                                        onclick="return confirm('Are you sure you want to hide this comment?')">Hide</button>
                                <?php endif; ?>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>