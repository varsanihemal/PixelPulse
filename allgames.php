<?php
session_start();
require ('includes/connect.php');
include ('fetch.php');

// Function to fetch games by category
function fetchByCategory($category)
{
    global $db;

    // Construct the SQL query to fetch games based on the category
    $query = "SELECT g.* FROM games g ";
    if (!empty($category)) {
        $query .= "JOIN categories c ON g.category_id = c.category_id WHERE c.category_name = :category";
    }

    // Prepare the query
    $statement = $db->prepare($query);

    // Bind the category parameter if it's provided
    if (!empty($category)) {
        $statement->bindParam(':category', $category);
    }

    // Execute the query
    $statement->execute();

    // Fetch the results
    $games = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $games;
}


// Fetch categories from the database
$query = "SELECT * FROM categories";
$statement = $db->query($query);
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="styles.css">
    <style>
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 20px;
        }

        .game {
            width: 200px;
            margin-bottom: 20px;
            text-align: center;
        }

        .game img {
            width: 200px;
            height: auto;
            margin-bottom: 10px;
        }
        .categories-dropdown ul {
            list-style: none;
        }
        .sort{
            padding-left: 30px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <?php include ('./includes/nav.php'); ?>

    <!-- Categories Dropdown -->
    <div class="categories-dropdown">
        <ul>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Categories
                </a>
                <ul class="dropdown-menu">
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a class="dropdown-item"
                                href="?category=<?= strtolower($category['category_name']) ?><?= isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '' ?>">
                                <?= $category['category_name'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li><a class="dropdown-item" href="?<?= isset($_GET['sort']) ? 'sort=' . $_GET['sort'] : '' ?>">View
                            All
                            Games</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Sorting options -->
    <?php if ($isLoggedIn): ?>
        <div class="sort">
            <a href="?category=<?= isset($_GET['category']) ? $_GET['category'] : '' ?>&sort=title">Sort by Title</a> |
            <a href="?category=<?= isset($_GET['category']) ? $_GET['category'] : '' ?>&sort=release_date">Sort by Release
                Date</a> |
            <a href="?category=<?= isset($_GET['category']) ? $_GET['category'] : '' ?>&sort=price">Sort by Price</a>
        </div>
    <?php endif; ?>

    <!-- Game Cards -->
    <div class="card-container">
        <?php foreach ($rows as $row): ?>
            <div class="game card">
                <?php if (!empty($row['cover_image_path'])): ?>
                    <a href="gamepage.php?id=<?= $row['game_id'] ?>&slug=<?= generateSlug($row['title']) ?>">
                        <img src="<?= $row['cover_image_path'] ?>" class="card-img-top" alt="">
                    </a>
                <?php endif; ?>
                <div class="card-body">
                    <a href="gamepage.php?id=<?= $row['game_id'] ?>&slug=<?= generateSlug($row['title']) ?>"
                        class="card-link">
                        <p class="card-text"><?= $row['title'] ?></p>
                    </a>
                    <?php if (isset($_GET['sort']) && $_GET['sort'] === 'release_date'): ?>
                        <p class="card-text">Release Date: <?= $row['release_date'] ?></p>
                    <?php elseif (isset($_GET['sort']) && $_GET['sort'] === 'price'): ?>
                        <p class="card-text">Price: <?= $row['price'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
