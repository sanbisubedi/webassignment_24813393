<?php
// session start
session_start();
// database extraction from db.php
require_once 'db.php';

// // Redirects to index.php if the user is not logged in as an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
       //redirect to index.php
    header("Location: index.php");
    exit;
}

// database connection
$db = getDbConnection();
//query to fetch data from category table
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

// Handle category addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_category'])) {
    // Fetches categories and updates auction details on form submission, then redirects to the auction page.
    $stmt = $db->prepare("INSERT INTO category (name) VALUES (?)");
     //adding and exectuing names
    $stmt->execute([$_POST['new_category']]);
       //redirecting to admin category page
    header("Location: adminCategories.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Manage Categories</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
    <header>
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>
        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit" value="Search" />
        </form>
    </header>

    <nav>
        <ul>
            <?php 
            $mainCategories = array_slice($categories, 0, 7); // First 7 categories
            $moreCategories = array_slice($categories, 7);    // Categories beyond 7
            foreach ($mainCategories as $category): ?>
                <li><a class="categoryLink" href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endforeach; ?>
            <?php if (!empty($moreCategories)): ?>
                <li>
                    <span class="categoryLink">More</span>
                    <ul class="dropdown">
                        <?php foreach ($moreCategories as $category): ?>
                            <li><a class="categoryLink" href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endif; ?>
            <li><a href="addAuction.php">Add Auction</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="adminCategories.php">Manage Categories</a></li>
        </ul>
    </nav>
    <img src="banners/1.jpg" alt="Banner" />

    <main>
        <h1>Manage Categories</h1>
        <form method="POST" style="display: flex; align-items: center; gap: 10px;">
            <input type="text" name="new_category" placeholder="New category name" required />
            <input type="submit" value="Add Category" />
        </form>
        <ul class="carList">
            <?php foreach ($categories as $category): ?>
                <li>
                    <article>
                        <h2><?php echo htmlspecialchars($category['name']); ?></h2>
                        <a href="editCategory.php?id=<?php echo $category['id']; ?>">Edit</a>
                        <a href="deleteCategory.php?id=<?php echo $category['id']; ?>">Delete</a>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>

    <footer>
        © Carbuy 2024
    </footer>
</body>
</html>