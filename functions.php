<?php
require ('./includes/connect.php');

// User Management Functions
function addUser($email, $password)
{
    global $db;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
    $statement = $db->prepare($query);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':password', $hashed_password);
    $statement->execute();
}

function updateUser($user_id, $email)
{
    global $db;
    $query = "UPDATE users SET email = :email WHERE user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':user_id', $user_id);
    $statement->execute();
}

function deleteUser($user_id)
{
    global $db;
    $query = "DELETE FROM users WHERE user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindParam(':user_id', $user_id);
    $statement->execute();
}

function getAllUsers()
{
    global $db;
    $query = "SELECT * FROM users";
    $statement = $db->query($query);
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Ensure all users have an 'email' key
    foreach ($users as &$user) {
        if (!isset($user['email'])) {
            $user['email'] = ''; // Set a default value if 'email' key is missing
        }
    }
    return $users;
}

// Category Management Functions

function updateCategory($category_id, $category_name, $description)
{
    global $db;
    $query = "UPDATE categories SET category_name = :category_name, description = :description WHERE category_id = :category_id";
    $statement = $db->prepare($query);
    $statement->bindParam(':category_id', $category_id);
    $statement->bindParam(':category_name', $category_name);
    $statement->bindParam(':description', $description);
    $statement->execute();
}

function getAllCategories()
{
    global $db;
    $query = "SELECT * FROM categories";
    $statement = $db->query($query);
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
}
?>