<?php
include ('fetch.php');

if (isset($_POST['submit'])) {
    $search = $_POST['search'];

    if (!preg_match("/^\d{4}$/", $search)) {
        $searchQuery = "SELECT * FROM games WHERE title LIKE :title";
        $searchParam = "%$search%"; // search for title
    } else {
        $searchQuery = "SELECT * FROM games WHERE release_date LIKE :release_date";
        $searchParam = "%$search%"; // search for release date by year
    }

    $stm = $db->prepare($searchQuery);

    if (!preg_match("/^\d{4}$/", $search)) {
        $stm->bindValue(':title', $searchParam, PDO::PARAM_STR); // Bind title search parameter
    } else {
        $stm->bindValue(':release_date', $searchParam, PDO::PARAM_STR); // Bind release date search parameter
    }

    $stm->execute();
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
</head>

<body>
    <?php
    include ('includes/nav.php');
    ?>
    <div class="container">
        <?php if ($stm->rowCount() > 0): ?>
            <h2>Search Results</h2>
            <div class="search-flex-container">
                <?php while ($row = $stm->fetch()): ?>
                    <div class="search-card">
                        <img width="150px" src="<?= $row['cover_image_path'] ?>" alt="">
                        <p>
                            <a href="gamepage.php?id=<?= $row['game_id'] ?>&slug=<?= generateSlug($row['title']) ?>">
                                <?= $row['title'] ?>
                            </a>
                        </p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No results found.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>