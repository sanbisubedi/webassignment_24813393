<?php
//login checked
session_start();
//database connection
require_once 'db.php';

// Redirects to index.php if the user is not logged in as an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    //redirect to index.php
    header("Location: index.php");
    exit;
}
//database connection
$db = getDbConnection();
//query to fetch data from category table
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

// Fetches categories and updates auction details on form submission, then redirects to the auction page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //inserting query for categories
    $stmt = $db->prepare("INSERT INTO category (name) VALUES (?)");
    //adding and exectuing names
    $stmt->execute([$_POST['name']]);
    //redirecting to admin category page
    header("Location: adminCategories.php");
    exit;
}
?>
<!-- html code from university -->
<!DOCTYPE html>
<!--  -->
<html>
<head>
    <!--add category title -->
    <title>Carbuy Auctions - Add Category</title>
    <!-- css link -->
    <link rel="stylesheet" href="carbuy.css" />
</head>
<!-- head ending -->
<body>
    <header>
        <!-- span to create different color for title -->
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>
        <!-- form for search bar -->
        <form action="search.php" method="GET">
            <!-- input for searching a car -->
            <input type="text" name="search" placeholder="Search for a car" />
            <!-- search button -->
            <input type="submit" name="submit" value="Search" />
        </form>
    </header>
  <!-- navigation bar -->
    <nav>
<!-- unorder list -->
        <ul>
           <!-- for each loop for category table -->
        <?php foreach ($categories as $category): ?>
            <!-- category name -->
                <li><a class="categoryLink" href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endforeach; ?>
            <!-- end for each -->
            <li><a href="addAuction.php">Add Auction</a></li>
            <!-- logout -->
            <li><a href="logout.php">Logout</a></li>
            <!-- manage category -->
            <li><a href="adminCategories.php">Manage Categories</a></li>
        </ul>
    </nav>
    <!-- end navigation bar -->

    <!-- genrating random image -->
    <img src="/images/randombanner.php"/>

    <!-- adding category -->
    <main>
        <!-- category title -->
        <h1>Add Category</h1>
        <!-- post method -->
        <form method="POST">
            <!-- input tag for category -->
            <label>Name</label> <input type="text" name="name" required />
            <!-- add button -->
            <input type="submit" value="Add Category" />
        </form>
    </main>
<!-- footer -->
    <footer>
        © Carbuy 2024
    </footer>
</body>
</html>