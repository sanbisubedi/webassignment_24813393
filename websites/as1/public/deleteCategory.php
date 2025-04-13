<?php
session_start();
require_once 'db.php';

// Restrict access to admin users only
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // redrrecting index.php
    header("Location: index.php");
    exit;
}

// Establish database connection
$db = getDbConnection();

// Get category ID from URL
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : null;
// if statement for categoryif
if (!$categoryId) {
    // Redirect with an error message if ID is missing
    header("Location: adminCategories.php?error=" . urlencode("Invalid category ID."));
    exit;
}

try {
    // Check if the category exists
    $stmt = $db->prepare("SELECT id FROM category WHERE id = ?");
//    execting above code
    $stmt->execute([$categoryId]);
    // fetcing data from category table
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
// checking category from category table
    if (!$category) {
        header("Location: adminCategories.php?error=" . urlencode("Category not found."));
        exit;
    }

    // Attempt to delete the category
    $deleteStmt = $db->prepare("DELETE FROM category WHERE id = ?");
    // execting delete query
    $deleteStmt->execute([$categoryId]);

    // Redirect with success message
    header("Location: adminCategories.php?success=" . urlencode("Category deleted successfully."));
    exit;
} catch (PDOException $e) {
    // Handle foreign key constraint violation (e.g., category is linked to auctions)
    if ($e->getCode() == '23000') {
        header("Location: adminCategories.php?error=" . urlencode("Cannot delete category because it is associated with existing auctions."));
    } else {
        header("Location: adminCategories.php?error=" . urlencode("An error occurred while deleting the category: " . $e->getMessage()));
    }
    exit;
}