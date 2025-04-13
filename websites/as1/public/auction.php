<?php
session_start();
// database extraction from db.php
require_once 'db.php';

// Get database connection
$db = getDbConnection();

// Get auction ID from URL
$auctionId = $_GET['id'] ?? null;
// if statement for auction id
if (!$auctionId) {
    // redirecting to index.php
    header('Location: index.php');
    exit();
}

// Fetch auction details and using join for to different table category and auction
$stmt = $db->prepare("SELECT a.*, c.name AS categoryName, u.name AS userName, u.id AS userId 
                     FROM auction a 
                     JOIN category c ON a.categoryId = c.id 
                     JOIN user u ON a.userId = u.id 
                     WHERE a.id = ?");
                    //  executing line number 19 code
$stmt->execute([$auctionId]);
// fetching data
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

// if statement for auction 
if (!$auction) {
    // redirecting index.php
    header('Location: index.php');
    exit();
}

// Handle bid submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid'])) {
    // checking if user is login or not
    if (!isset($_SESSION['user_id'])) {
        // is not login redirect login.php
        header('Location: login.php');
        exit();
    }
    // adding bid 
    $amount = $_POST['bid'];
    // adding user id
    $userId = $_SESSION['user_id'];
    // query to insert above details 
    $bidStmt = $db->prepare("INSERT INTO bid (amount, auctionId, userId) VALUES (?, ?, ?)");
    // executing line 19 code 
    $bidStmt->execute([$amount, $auctionId, $userId]);
    // redirecting to auction.php after completion
    header("Location: auction.php?id=$auctionId");
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewText'])) {
    // user handling
    if (!isset($_SESSION['user_id'])) {
        // redirecting to login.php 
        header('Location: login.php');
        exit();
    }
    // adding review text
    $reviewText = $_POST['reviewText'];
    // adding user id
    $userId = $_SESSION['user_id'];
    // adding reviewed id
    $reviewedUserId = $auction['userId'];
    // inserting above details
    $reviewStmt = $db->prepare("INSERT INTO review (reviewText, userId, reviewedUserId, datePosted) VALUES (?, ?, ?, NOW())");
    // executing above details
    $reviewStmt->execute([$reviewText, $userId, $reviewedUserId]);
    // redirecting to auction.php
    header("Location: auction.php?id=$auctionId");
    exit();
}

// Fetch reviews for the auction's author
$reviewsStmt = $db->prepare("SELECT r.*, u.name AS reviewerName 
                            FROM review r 
                            JOIN user u ON r.userId = u.id 
                            WHERE r.reviewedUserId = ?");
                            // review statement executed
$reviewsStmt->execute([$auction['userId']]);
// fetching review statement
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

// statement to Fetch highest bid
$highestBidStmt = $db->prepare("SELECT MAX(amount) as highestBid FROM bid WHERE auctionId = ?");
// executing line number 91 code
$highestBidStmt->execute([$auctionId]);
// fetching highest bid amount
$highestBid = $highestBidStmt->fetch(PDO::FETCH_ASSOC)['highestBid'];

// Calculate time left
$endDate = new DateTime($auction['endDate']);
// adding date and time
$now = new DateTime();
// adding current date and time
$interval = $now->diff($endDate);
// date and time in hours and min format
$timeLeft = $interval->format('%h hours %i minutes');

// select and fetching categories queries
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - <?php echo htmlspecialchars($auction['title']); ?></title>
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
    <img src="banners/1.jpg" alt="Banner" />

    <main>
        <h1>Car Page</h1>
        <article class="car">
            <img src="car.png" alt="<?php echo htmlspecialchars($auction['title']); ?>">
            <section class="details">
                <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                <h3><?php echo htmlspecialchars($auction['categoryName']); ?></h3>
                <p>Auction created by <a href="userReviews.php?userId=<?php echo $auction['userId']; ?>"><?php echo htmlspecialchars($auction['userName']); ?></a></p>
                <p class="price">Current bid: £<?php echo $highestBid ?: '0.00'; ?></p>
                <time>Time left: <?php echo $timeLeft; ?></time>
                <form action="auction.php?id=<?php echo $auctionId; ?>" method="POST" class="bid">
                    <input type="text" name="bid" placeholder="Enter bid amount" required />
                    <input type="submit" value="Place bid" />
                </form>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $auction['userId']): ?>
                    <p><a href="editAuction.php?id=<?php echo $auctionId; ?>">Edit Auction</a></p>
                <?php endif; ?>
            </section>
            <section class="description">
                <p><?php echo htmlspecialchars($auction['description']); ?></p>
            </section>
            <section class="reviews">
                <h2>Reviews of <?php echo htmlspecialchars($auction['userName']); ?></h2>
                <ul>
                    <?php foreach ($reviews as $review): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($review['reviewerName']); ?> said </strong>
                            <?php echo htmlspecialchars($review['reviewText']); ?>
                            <em>
                                <?php 
                                // Handle null datePosted safely
                                if ($review['datePosted']) {
                                    $reviewDate = new DateTime($review['datePosted']);
                                    echo $reviewDate->format('d/m/Y');
                                } else {
                                    echo 'Date unavailable';
                                }
                                ?>
                            </em>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="auction.php?id=<?php echo $auctionId; ?>" method="POST">
                        <label>Add your review</label>
                        <textarea name="reviewText" required></textarea>
                        <input type="submit" name="submit" value="Add Review" />
                    </form>
                <?php endif; ?>
            </section>
        </article>
    </main>

    <footer>
        © Carbuy 2024
    </footer>
</body>
</html>