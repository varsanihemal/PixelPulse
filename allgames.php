<?php
session_start();
require ('includes/connect.php');
include ('fetch.php');

// Function to fetch games by category
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

// Sort functions
function sortTitle($a, $b)
{
    return strcmp($a['title'], $b['title']);
}

function sortReleaseDate($a, $b)
{
    return strcmp($a['release_date'], $b['release_date']);
}

function sortPrice($a, $b)
{
    return strcmp($a['price'], $b['price']);
}

// Fetch games based on category
$rows = fetchByCategory(isset($_GET['category']) ? $_GET['category'] : null);

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Sort the games if sort parameter is provided in the URL and if user is logged in
if ($isLoggedIn && isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'title':
            usort($rows, 'sortTitle');
            break;
        case 'release_date':
            usort($rows, 'sortReleaseDate');
            break;
        case 'price':
            usort($rows, 'sortPrice');
            break;
    }
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
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="./utilities/viewall.css">

</head>

<body>

    <?php include ('./includes/nav.php'); ?>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Categories
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item"
                    href="?category=action<?= isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '' ?>">Action</a>
            </li>
            <li><a class="dropdown-item"
                    href="?category=adventure<?= isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '' ?>">Adventure</a>
            </li>
            <li><a class="dropdown-item"
                    href="?category=sports<?= isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '' ?>">Sports/Racing</a>
            </li>
            <li><a class="dropdown-item" href="?<?= isset($_GET['sort']) ? 'sort=' . $_GET['sort'] : '' ?>">View All
                    Games</a></li>
        </ul>
    </li>

    <!-- Sorting options -->
    <?php if ($isLoggedIn): ?>
        <div class="sort">
            <a href="?category=<?= isset($_GET['category']) ? $_GET['category'] : '' ?>&sort=title">Sort by Title</a> |
            <a href="?category=<?= isset($_GET['category']) ? $_GET['category'] : '' ?>&sort=release_date">Sort by Release
                Date</a> |
            <a href="?category=<?= isset($_GET['category']) ? $_GET['category'] : '' ?>&sort=price">Sort by Price</a>
        </div>
    <?php endif; ?>

    <div class="card-container">
        <?php foreach ($rows as $row): ?>
            <div class="game">
                <?php if (!empty($row['cover_image_path'])): ?>
                    <a href="gamepage.php?id=<?= $row['game_id'] ?>&slug=<?= $row['slug'] ?>">
                        <!-- <img src="<?= $row['cover_image_path'] ?>" alt=""> -->
                    </a>
                <?php endif; ?>
                <a href="gamepage.php?id=<?= $row['game_id'] ?>&slug=<?= $row['slug'] ?>">
                    <p><?= $row['title'] ?></p>
                </a>
                <?php if (isset($_GET['sort']) && $_GET['sort'] === 'release_date'): ?>
                    <p>Release Date: <?= $row['release_date'] ?></p>
                <?php elseif (isset($_GET['sort']) && $_GET['sort'] === 'price'): ?>
                    <p>Price: <?= $row['price'] ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>