<?php
// session check
session_start();
// database connection
require_once 'db.php';

// post method for registration process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // establishing database connection from db.php
    $db = getDbConnection();
    // adding email password and name for registration process
    $stmt = $db->prepare("INSERT INTO user (email, password, name) VALUES (?, ?, ?)");
    // saving above entered details
    $stmt->execute([$_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['name']]);
    // redirection to login page
    header("Location: login.php");
    exit;

}

// establishing database connection from db.php
$db = getDbConnection();
// select query from category table
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Register</title>
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
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>
    <img src="images/randombanner.php"/>

    <main>
        <h1>Register</h1>
        <form method="POST">
            <label>Email</label> <input type="email" name="email" required />
            <label>Password</label> <input type="password" name="password" required />
            <label>Name</label> <input type="text" name="name" required />
            <input type="submit" value="Register" />
        </form>
    </main>

    <footer>
        © Carbuy 2024
    </footer>
</body>
</html>