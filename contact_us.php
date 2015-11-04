<?php 
    include './includes/title.php'; 
    $errors = [];
    $missing = [];
    if (isset($_POST['send'])) {
        $to = 'csmith@northpointdigital.com';
        $subject = 'Feedback from Japan\'s Journey';
        $expected = ['name', 'email', 'comments'];
        $required = ['name', 'comments', 'email', 'subscribe'];
        if (!isset($_POST['subscribe'])) {
            $_POST['subscribe'] = '';
        }
        $headers = "From: Japan Journey<feedback@example.com>\r\n";
        $headers .= 'Content-Type: text/plain; charset=utf-8';
        require './includes/processmail.php';

        if($mailSent) {
            header('Location: http://localhost/~csmith/project/thank_you.php');
            exit;
        }
    }
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Japan Journey<?php if (isset($title)) echo "&#8212;{$title}"; ?></title>
    <link href="styles/journey.css" rel="stylesheet" type="text/css">
</head>

<body>
<header>
    <h1>Japan Journey</h1>
</header>
<div id="wrapper">
    <?php require './includes/menu.php'; ?>
    <main>
        <h2>Contact Us  </h2>
        <?php if (($_POST && $suspect) || ($_POST && isset($errors['mailfail']))) { ?>
            <p class="warning">Sorry, your mail could not be sent.
        Please try later.</p>
        <?php } elseif ($missing || $errors) { ?>
            <p class="warning">Please fix the item(s) indicated.</p>
        <?php } ?>
      <p>Ut enim ad minim veniam, quis nostrud exercitation consectetur adipisicing elit. Velit esse cillum dolore ullamco laboris nisi in reprehenderit in voluptate. Mollit anim id est laborum. Sunt in culpa duis aute irure dolor excepteur sint occaecat.</p>
        <form method="post" action="">
            <p>
                <label for="name">Name:
                    <?php if ($missing && in_array('name', $missing)) {?>
                        <span class="warning">  Please enter your name</span>
                    <?php } ?>
                </label>
                <input name="name" id="name" type="text"
                <?php if ($missing || $errors) {
                    echo 'value="' . htmlentities($name) . '"';
                } ?>>
            </p>
            <br/>
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
            <br/>
            <p>
                <label for="comments">Comments:
                    <?php if ($missing && in_array('comments', $missing)) {?>
                        <span class="warning">  You must include a comment</span>
                    <?php } ?>
                </label>
                <textarea name="comments" id="comments">
                    <?php if ($missing || $errors) {
                        htmlentities($comments);
                    } ?>
                </textarea>
            </p>
            <fieldset id="subscribe">
                <h2>Subscribe to newsletter?
                    <?php if($missing && in_array('subscribe', $missing)) {?>
                    <span class="warning">Please make a selection</span>
                    <?php } ?>    
                </h2>
                <p>
                    <input name="subscribe" type="radio" value="Yes" id="subscribe-yes"
                        <?php
                        if ($_POST && $_POST['subscribe'] == 'Yes') {
                        echo 'checked';
                        } ?>
                    >
                    <label for="subscribe-yes">Yes</label>
                    <input name="subscribe" type="radio" value="No" id="subscribe-no"
                        <?php
                        if (!$_POST || $_POST['subscribe'] == 'No') {
                        echo 'checked';
                        } ?>
                    >
                    <label for="subscribe-no">No</label>
                </p>
            </fieldset>
            <p>
                <input name="send" type="submit" value="Send message">
            </p>
        </form>
        <pre>
            <?php if ($_POST && $mailSent) {
                echo "Message body\n\n";
                echo htmlentities($message) . "\n";
                echo 'Headers: '. htmlentities($headers);
            } ?>
        </pre>
    </main>
    <?php include './includes/footer.php'; ?>
</div>
</body>
</html>
