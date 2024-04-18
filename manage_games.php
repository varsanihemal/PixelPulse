<?php
session_start();
include ('./includes/connect.php');

// Initialize error message variable
$error_message = '';

// Fetch existing games
$query = "SELECT * FROM games";
$statement = $db->query($query);
$games = $statement->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing categories
$queryCategories = "SELECT * FROM categories";
$statementCategories = $db->query($queryCategories);
$categories = $statementCategories->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for adding a new game
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_game'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $release_date = $_POST['release_date'];
    $image_path = ''; // Initialize image path

    // Initialize error upload flag
    $error_upload = false;

    // Handle image upload
    if ($_FILES['game_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['game_image']['tmp_name'];
        $file_name = $_FILES['game_image']['name'];
        $file_type = $_FILES['game_image']['type'];

        // Check if the file type is PDF
        if ($file_type === 'application/pdf') {
            $error_message .= "PDF files are not allowed.";
            $error_upload = true; // Set error upload flag
        } else {
            // Testing if the uploaded file is an image
            $allowed_types = ['image/jpeg', 'image/png', 'image/avif'];
            if (in_array($file_type, $allowed_types)) {
                // Moving uploaded file to uploads directory
                $uploads_dir = 'uploads/';
                $target_path = $uploads_dir . $file_name;
                if (move_uploaded_file($file_tmp_name, $target_path)) {
                    $image_path = $target_path;

                    // Resize uploaded image
                    list($width, $height) = getimagesize($image_path);
                    $new_width = 200;
                    $new_height = 266;

                    // Resampling
                    switch ($file_type) {
                        case 'image/jpeg':
                            $image_source = imagecreatefromjpeg($image_path);
                            break;
                        case 'image/png':
                            $image_source = imagecreatefrompng($image_path);
                            break;
                        case 'image/avif':
                            $image_source = imagecreatefromavif($image_path);
                            break;
                    }

                    $image_resized = imagecreatetruecolor($new_width, $new_height);
                    imagecopyresampled($image_resized, $image_source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                    // Save resized image
                    $resized_image_path = $uploads_dir . 'resized_' . $file_name;
                    switch ($file_type) {
                        case 'image/jpeg':
                            imagejpeg($image_resized, $resized_image_path);
                            break;
                        case 'image/png':
                            imagepng($image_resized, $resized_image_path);
                            break;
                        case 'image/avif':
                            imageavif($image_resized, $resized_image_path);
                            break;
                    }

                    // Update $image_path to point to the resized image
                    $image_path = $resized_image_path;

                    // Free up memory
                    imagedestroy($image_source);
                    imagedestroy($image_resized);
                } else {
                    $error_message .= "Failed to move uploaded file.";
                    $error_upload = true; // Set error upload flag
                }
            } else {
                $error_message .= "Uploaded file is not a valid image.";
                $error_upload = true; // Set error upload flag
            }
        }
    } else {
        $error_message .= "Error uploading file.";
        $error_upload = true; // Set error upload flag
    }

    // Validate form data
    if (empty($title) || empty($description) || empty($price) || empty($category_id) || $error_upload) {
        $error_message .= "All fields are required.";
    } else {
        // Insert the game details into the database
        $query = "INSERT INTO games (category_id, title, description, price, release_date, cover_image_path) VALUES (:category_id, :title, :description, :price, :release_date, :cover_image_path)";
        $statement = $db->prepare($query);
        $statement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':description', $description, PDO::PARAM_STR);
        $statement->bindParam(':price', $price, PDO::PARAM_STR);
        $statement->bindParam(':release_date', $release_date, PDO::PARAM_STR); 
        $statement->bindParam(':cover_image_path', $image_path, PDO::PARAM_STR);

        if ($statement->execute()) {
            // Redirect only if the game is successfully added
            header("Location: allgames.php");
            exit;
        } else {
            // No redirection in case of error, handle it below
            $error_message .= "Failed to add the game. Please try again.";
        }
    }
}

// Handle form submission for adding a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];

    // Validate form data
    if (empty($category_name) || empty($description)) {
        $error_message .= "All fields are required.";
    } else {
        // Insert the category details into the database
        $query = "INSERT INTO categories (category_name, description) VALUES (:category_name, :description)";
        $statement = $db->prepare($query);
        $statement->bindParam(':category_name', $category_name, PDO::PARAM_STR);
        $statement->bindParam(':description', $description, PDO::PARAM_STR);

        if ($statement->execute()) {
            header("Location: manage_games.php");
            exit;
        } else {
            $error_message .= "Failed to add the category. Please try again.";
        }
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
    <title>Manage Games</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <?php include ('./includes/nav.php'); ?>
    <div class="container">
        <h2>Add Game</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $error_message ?>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="release_date" class="form-label">Release Date</label>
                <input type="date" class="form-control" id="release_date" name="release_date" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="game_image" class="form-label">Game Image</label>
                <input type="file" class="form-control" id="game_image" name="game_image">
            </div>

            <button type="submit" class="btn btn-primary" name="add_game">Add Game</button>
        </form>

        <h2>Add Category</h2>
        <form method="post">
            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <div class="mb-3">
                <label for="category_description" class="form-label">Category Description</label>
                <textarea class="form-control" id="category_description" name="description" rows="3"
                    required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="add_category">Add Category</button>
        </form>

        <h2>Existing Games</h2>
        <div class="row">
            <?php foreach ($games as $game): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= $game['title'] ?></h5>
                            <p class="card-text">Price: <?= $game['price'] ?></p>
                            <!-- Display other game details -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="gamepage.php?id=<?= $game['game_id'] ?>&slug=<?= generateSlug($row['title']) ?>"
                                    class="btn btn-primary">View Game</a>
                                <a href="editgame.php?id=<?= $game['game_id'] ?>" class="btn btn-primary">Edit</a>
                                <form method="post" action="deletegame.php">
                                    <input type="hidden" name="game_id" value="<?= $game['game_id'] ?>">
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this game?')">Delete</button>
                                </form>
                            </div>

                        </div>
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