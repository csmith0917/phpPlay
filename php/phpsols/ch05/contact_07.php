<?php
include './includes/title.php';
$errors = [];
$missing = [];
// check if the form has been submitted
if (isset($_POST['send'])) {
    // email processing script
    $to = 'david@example.com';
    $subject = 'Feedback from Japan Journey';
    // list expected fields
    $expected = ['name', 'email', 'comments'];
    $required = ['name', 'comments', 'email'];
    // create additional headers
    $headers = "From: Japan Journey<feedback@example.com>\r\n";
    $headers .= 'Content-Type: text/plain; charset=utf-8';
    require './includes/processmail.php';
    if ($mailSent) {
        header('Location: http://www.example.com/thank_you.php');
        exit;
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Japan Journey<?php if(isset($title)) { echo "&#8212;{$title}"; } ?></title>
    <link href="styles/journey.css" rel="stylesheet" type="text/css">
</head>

<body>
<header>
    <h1>Japan Journey </h1>
</header>
<div id="wrapper">
    <?php require './includes/menu.php'; ?>
    <main>
        <h2>Contact Us</h2>
        <?php if (($_POST && $suspect) || ($_POST && isset($errors['mailfail']))) {  ?>
            <p class="warning">Sorry, your mail could not be sent.
                Please try later.</p>
        <?php } elseif ($missing || $errors) { ?>
            <p class="warning">Please fix the item(s) indicated.</p>
        <?php } ?>
        <p>Ut enim ad minim veniam, quis nostrud exercitation consectetur adipisicing elit. Velit esse cillum dolore ullamco laboris nisi in reprehenderit in voluptate. Mollit anim id est laborum. Sunt in culpa duis aute irure dolor excepteur sint occaecat.</p>
        <form method="post" action="">
            <p>
                <label for="name">Name:
                    <?php if ($missing && in_array('name', $missing)) { ?>
                        <span class="warning">Please enter your name</span>
                    <?php } ?>
                </label>
                <input name="name" id="name" type="text"
                <?php if ($missing || $errors) {
                    echo 'value="' . htmlentities($name) . '"';
                } ?>>
            </p>
            <p>
                <label for="email">Email:
                    <?php if ($missing && in_array('email', $missing)) { ?>
                        <span class="warning">Please enter your email address</span>
                    <?php } elseif (isset($errors['email'])) { ?>
                        <span class="warning">Invalid email address</span>
                    <?php } ?>
                </label>
                <input name="email" id="email" type="text"
                <?php if ($missing || $errors) {
                    echo 'value="' . htmlentities($email) . '"';
                } ?>>
            </p>
            <p>
                <label for="comments">Comments:
                    <?php if ($missing && in_array('comments', $missing)) { ?>
                        <span class="warning">Please enter your comments</span>
                    <?php } ?>
                </label>
                <textarea name="comments" id="comments"><?php
                    if ($missing || $errors) {
                        echo htmlentities($comments);
                    } ?></textarea>
            </p>
            <p>
                <input name="send" type="submit" value="Send message">
            </p>
        </form>
    </main>
    <?php include './includes/footer.php'; ?>
</div>
</body>
</html>
