<?php
session_start();
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);

require ('includes/connect.php');

$query1 = "SELECT * FROM games WHERE category_id = 1";
$statement_action = $db->prepare($query1);
$statement_action->execute();

$query2 = "SELECT * FROM games WHERE category_id = 2";
$statement_adventure = $db->prepare($query2);
$statement_adventure->execute();

$query3 = "SELECT * FROM games WHERE category_id = 3";
$statement_sports = $db->prepare($query3);
$statement_sports->execute();

// Function to generate slug from title
function generateSlug($title)
{
    return strtolower(str_replace(' ', '-', $title));
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
    <link rel="stylesheet" href="./utilities/body.css">
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    <?php
    include ('includes/nav.php');
    ?>

    <div class="container-index">
        <div class="intro">
            <h1>
                Welcome to PixelPulse Store.
            </h1>
            <p>
                Welcome to the world of gaming, where adventure knows no bounds and challenges await at every turn!
                Whether you're a seasoned gamer seeking your next epic quest or a newcomer ready to embark on your very
                first adventure, you've come to the right place.
            </p>
        </div>
        <div class="game-display">
            <div class="games">
                <div class="custom-card">
                    <!-- Displaying action game images -->
                    <div class="action">
                        <?php while ($row = $statement_action->fetch()): ?>
                            <a href="gamepage.php?id=<?= $row['game_id'] ?>&slug=<?= generateSlug($row['title']) ?>">
                                <img src="<?= $row['cover_image_path'] ?>" alt="">
                            </a>
                        <?php endwhile; ?>
                    </div>

                    <!-- Displaying adventure game images -->
                    <div class="adventure">
                        <?php while ($row = $statement_adventure->fetch()): ?>
                            <a href="gamepage.php?id=<?= $row['game_id'] ?>&slug=<?= generateSlug($row['title']) ?>">
                                <img src="<?= $row['cover_image_path'] ?>" alt="">
                            </a>
                        <?php endwhile; ?>
                    </div>

                    <!-- Displaying Sports/Racing games images -->
                    <div class="sports">
                        <?php while ($row = $statement_sports->fetch()): ?>
                            <a href="gamepage.php?id=<?= $row['game_id'] ?>&slug=<?= generateSlug($row['title']) ?>">
                                <img src="<?= $row['cover_image_path'] ?>" alt="">
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cyberpunk-bg">
        <img src="./images/cyber.png " alt="">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>