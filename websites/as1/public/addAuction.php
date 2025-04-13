<?php
// Initializes a session, loads database configuration, and redirects to login.php if no user is logged in
session_start();
// database connection from db.php
require_once 'db.php';

// checking for login user
if (!isset($_SESSION['user_id'])) {
    // redirecting to loing page
    header("Location: login.php");
    exit;
}
// Connects to the database and collect all categories from the category table as an associative array
$db = getDbConnection();
// select query for category table
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

// post method for car auction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // adding title
    $title = $_POST['title'];
    // adding description
    $description = $_POST['description'];
    // adding category
    $categoryId = $_POST['category'];
    // adding auction_end_date
    $endDate = $_POST['auction_end_date'];
    // adding user_id
    $userId = $_SESSION['user_id'];

    // insertingquery for title category and other details from above post method
    $stmt = $db->prepare("INSERT INTO auction (title, description, categoryId, endDate, userId) VALUES (?, ?, ?, ?, ?)");
    // executing above code for adding data
    $stmt->execute([$title, $description, $categoryId, $endDate, $userId]);
    // refirecting to index.php after completing post method
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Add Auction</title>
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
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <li><a href="adminCategories.php">Manage Categories</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <img src="images/randombanner.php"/>

    <main>
        <h1>Add Auction</h1>
        <form method="POST">
            <label>Title</label> 
            <input type="text" name="title" required placeholder="Enter car model and make" />
            
            <label>Description</label> 
            <textarea name="description" required placeholder="Describe the car"></textarea>
            
            <label>Category</label>
            <select name="category" required>
                <option value="" disabled selected>Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <label>End Date/Time</label> 
            <input type="datetime-local" name="auction_end_date" required />
            
            <input type="submit" value="Add Auction" />
        </form>
    </main>

    <footer>
        © Carbuy 2024
    </footer>
</body>
</html>