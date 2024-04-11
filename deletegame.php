<?php
session_start();
require ('includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['game_id'])) {
        $gameId = filter_input(INPUT_POST, 'game_id', FILTER_VALIDATE_INT);

        if ($gameId === false || $gameId === null || $gameId <= 0) {
            header("Location: allgames.php");
            exit;
        }

        // Delete the game from the database
        $query = "DELETE FROM games WHERE game_id = :id";
        $statement = $db->prepare($query);
        $statement->bindParam(':id', $gameId, PDO::PARAM_INT);

        if ($statement->execute()) {
            // Redirect user to allgames.php after successful deletion
            header("Location: allgames.php");
            exit;
        } else {
            header("Location: allgames.php");
            exit;
        }
    } else {
        header("Location: allgames.php");
        exit;
    }
} else {
    header("Location: allgames.php");
    exit;
}
?>
