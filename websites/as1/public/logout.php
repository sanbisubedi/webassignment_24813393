<?php
// session for login user
session_start();
// terminating logined user
session_destroy();
// redirecting to index page
header("Location: /index.php");
exit;
?>