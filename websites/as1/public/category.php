<?php
session_start();
require_once 'db.php';

// Retrieves auctions for a specific category, including auction details, category name, and user name.
// Uses prepared statements to safely query auctions by category ID,name, and retrieves all categories for display.
$db = getDbConnection();
// getiing category id for URL
$categoryId = $_GET['id'];
// Fetch auction details and using join for to different table category and auction
$stmt = $db->prepare("SELECT a.*, c.name AS categoryName, u.name AS userName 
                      FROM auction a 
                      JOIN category c ON a.categoryId = c.id 
                      JOIN user u ON a.userId = u.id 
                      WHERE a.categoryId = ?");
                    //   executing line number 11 code
$stmt->execute([$categoryId]);
// fetching all data of category
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
// select query for category 12
$categoryName = $db->query("SELECT name FROM category WHERE id = $categoryId")->fetchColumn();
// fetching data from category table
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - <?php echo htmlspecialchars($categoryName); ?></title>
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="addAuction.php">Add Auction</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <li><a href="adminCategories.php">Manage Categories</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <img src="images/randombanner.php"/>

    <main>
        <h1><?php echo htmlspecialchars($categoryName); ?> Listings</h1>
        <ul class="carList">
            <?php foreach ($auctions as $auction): ?>
                <li>
                    <img src="car.png" alt="<?php echo htmlspecialchars($auction['title']); ?>">
                    <article>
                        <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                        <h3><?php echo htmlspecialchars($auction['categoryName']); ?></h3>
                        <p><?php echo htmlspecialchars($auction['description']); ?></p>
                        <?php
                        $highestBid = $db->query("SELECT MAX(amount) FROM bid WHERE auctionId = {$auction['id']}")->fetchColumn();
                        ?>
                        <p class="price">Current bid: £<?php echo $highestBid ?: '0.00'; ?></p>
                        <a href="auction.php?id=<?php echo $auction['id']; ?>" class="more auctionLink">More >></a>
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