<?php
session_start();
require_once 'db.php';

// Checks if a user is logged in by verifying the existence of a user_id in the session.
// Redirects to the login page and terminates the script if no user_id is found.
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$db = getDbConnection();
$auctionId = $_GET['id'];
// Queries the database for an auction with the specified ID that belongs to the logged-in user.
$auction = $db->query("SELECT * FROM auction WHERE id = $auctionId AND userId = {$_SESSION['user_id']}")->fetch(PDO::FETCH_ASSOC);
// Redirects to the index page and exits if no matching auction is found.
if (!$auction) {
    header("Location: /index.php");
    exit;
}

$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

// Checks if the request is a POST submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepares a statement to update auction details in the database.
    $stmt = $db->prepare("UPDATE auction SET title = ?, description = ?, categoryId = ?, endDate = ? WHERE id = ?");
    // Executes the update with form data and auction ID.
    $stmt->execute([$_POST['title'], $_POST['description'], $_POST['category'], $_POST['auction_end_date'], $auctionId]);
    // Redirects to the auction page with the updated auction ID.
    header("Location: /auction.php?id=$auctionId");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Auction - Car Auction</title>
    <link rel="stylesheet" href="carbuy.css">
</head>
<body>
    <header>
        <!-- Same nav as index.php -->
    </header>
    <main>
        <h1>Edit Auction</h1>
        <form method="POST">
            <input type="text" name="title" value="<?php echo htmlspecialchars($auction['title']); ?>" required>
            <textarea name="description" required><?php echo htmlspecialchars($auction['description']); ?></textarea>
            <select name="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $auction['categoryId'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="datetime-local" name="auction_end_date" value="<?php echo str_replace(' ', 'T', $auction['endDate']); ?>" required>
            <button type="submit">Update Auction</button>
        </form>
    </main>
</body>
</html>