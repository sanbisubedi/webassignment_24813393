<?php
// session for login check
session_start();
// database connection code from db.php
require_once 'db.php';

// post method for login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // database connection
    $db = getDbConnection();
    // selecting user on the basis of email
    $stmt = $db->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    // fetching data from user table
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // verifying user email and password
    if ($user && password_verify($_POST['password'], $user['password'])) {
        // session for user 
        $_SESSION['user_id'] = $user['id'];
        // session for admin
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: index.php");
        exit;
    }
}
// database connection
$db = getDbConnection();
// fetching categories from category table
$categories = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>

<!--HTML Code -->
<!DOCTYPE html>
<html>
<head>
    <!-- Tttle for  page -->
    <title>Carbuy Auctions - Login</title>
    <!-- css link -->
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
    <!-- header starts here -->
    <header>
        <!-- different colored title -->
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>
       <!-- form for search option -->
        <form action="search.php" method="GET">
            <!-- input field for serarch -->
            <input type="text" name="search" placeholder="Search for a car" />
            <!-- submit button -->
            <input type="submit" name="submit" value="Search" />
        </form>
    </header>
    <!-- header ending -->

    <!-- nav bar for category -->
    <nav>
        <ul>
            <!-- php code with link for different links -->
            <?php foreach ($categories as $category): ?>
                <li><a class="categoryLink" href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endforeach; ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>
    <!-- nav bar ends here -->

    <!-- random banner for image rotation -->
    <img src="images/randombanner.php"/>

    <!-- login form -->
    <main>
        <h1>Login</h1>
        <!-- login for login process -->
        <form method="POST">
            <!-- email input field -->
            <label>Email</label> <input type="email" name="email" required />
            <!-- password input field -->
            <label>Password</label> <input type="password" name="password" required />
            <!-- login button -->
            <input type="submit" value="Login" />
        </form>
    </main>

    <footer>
        © Carbuy 2024
    </footer>
</body>
</html>