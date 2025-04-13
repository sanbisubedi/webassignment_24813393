<?php
// session for login check
session_start();
// database connection from db.php
require_once 'db.php';

// checking for if user is admin or not
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // redirectiong to index.php is user is admin
    header("Location: index.php");
    exit;
}

// database connection
$db = getDbConnection();
// getting user ID for particular user
$categoryId = $_GET['id'];
// selecting and fetching data from and for category table
$category = $db->query("SELECT * FROM category WHERE id = $categoryId")->fetch(PDO::FETCH_ASSOC);
// fetching query
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

// Updates a category's name with form data and redirects to the admin categories page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // update query to edit existing code
    $stmt = $db->prepare("UPDATE category SET name = ? WHERE id = ?");
    // update query exection
    $stmt->execute([$_POST['name'], $categoryId]);
    // redirect to admincategories.php after edit 
    header("Location: adminCategories.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Edit Category</title>
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
            <?php foreach ($categories as $category): ?>
                <li><a class="categoryLink" href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endforeach; ?>
            <li><a href="addAuction.php">Add Auction</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="adminCategories.php">Manage Categories</a></li>
        </ul>
    </nav>
    <img src="banners/1.jpg" alt="Banner" />

    <main>
        <h1>Edit Category</h1>
        <form method="POST">
            <label>Name</label> <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required />
            <input type="submit" value="Update Category" />
        </form>
    </main>

    <footer>
        © Carbuy 2024
    </footer>
</body>
</html>