
<?php
// database connection imported from dd.php
require_once 'db.php';

// main database conection
$db = getDbConnection();
// database query
$stmt = $db->query("SELECT a.*, c.name AS categoryName, u.name AS userName 
                    FROM auction a 
                    JOIN category c ON a.categoryId = c.id 
                    JOIN user u ON a.userId = u.id 
                    WHERE a.endDate > NOW() 
                    ORDER BY TIMESTAMPDIFF(SECOND, NOW(), a.endDate) ASC 
                    LIMIT 10");
// extracting data from database using fetch query
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// extracting data from categories tabse using fetch query
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- HTML CODE -->
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
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
            <!--  -->
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
    <!-- <img src="banners/1.jpg" alt="Banner" /> -->
     <!-- Here the random image appears -->
     <img src="images/randombanner.php"/>

    <main>
        <h1>Latest Car Listings</h1>
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
                        $endDate = new DateTime($auction['endDate']);
                        $now = new DateTime();
                        $interval = $now->diff($endDate);
                        $timeLeft = $interval->format('%h hours %i minutes');
                        ?>
                        <p class="price">Current bid: £<?php echo $highestBid ?: '0.00'; ?></p>
                        <time>Time left: <?php echo $timeLeft; ?></time>
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