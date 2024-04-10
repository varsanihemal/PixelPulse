<?php
session_start();
include ('fetch.php');

function fetchByCategory($category)
{

    global $statement_action, $statement_adventure, $statement_sports;

    switch ($category) {
        case 'action':
            return $statement_action->fetchAll();
        case 'adventure':
            return $statement_adventure->fetchAll();
        case 'sports':
            return $statement_sports->fetchAll();
        default:
            return array_merge($statement_action->fetchAll(), $statement_adventure->fetchAll(), $statement_sports->fetchAll());
    }

}

$rows = fetchByCategory(isset($_GET['category']) ? $_GET['category'] : null);
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
    <link rel="stylesheet" href="./utilities/viewall.css">

</head>

<body>

    <?php
    include ('./includes/nav.php');
    ?>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Categories
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="?category=action">Action</a></li>
            <li><a class="dropdown-item" href="?category=adventure">Adventure</a></li>
            <li><a class="dropdown-item" href="?category=sports">Sports/Racing</a></li>
        </ul>
    </li>

    <div class="card-container">
        <div class="game-card">
            <?php foreach ($rows as $row): ?>
                <div class="game">
                    <?php if (!empty($row['cover_image_path'])): ?>
                        <a href="gamepage.php?id=<?= $row['game_id'] ?>">
                            <img src="<?= $row['cover_image_path'] ?>" alt="">
                        </a>
                    <?php endif; ?>
                    <div class="card-body">

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