<?php
require_once '../includes/connection.php';
// initialize flags
$OK = false;
$done = false;
// create database connection
$conn = dbConnect('write');
// initialize statement
$stmt = $conn->stmt_init();
// get details of selected record
if (isset($_GET['article_id']) && !$_POST) {
    // prepare SQL query
    $sql = 'SELECT article_id, image_id, title, article
            FROM blog WHERE article_id = ?';
    if ($stmt->prepare($sql)) {
        // bind the query parameter
        $stmt->bind_param('i', $_GET['article_id']);
        // execute the query, and fetch the result
        $OK = $stmt->execute();
        // bind the results to variables
        $stmt->bind_result($article_id, $image_id, $title, $article);
        $stmt->fetch();
        // free the database resources for the second query
        $stmt->free_result();
        // get categories associated with the article
        $sql = 'SELECT cat_id FROM article2cat WHERE article_id = ?';
        if ($stmt->prepare($sql)) {
            $stmt->bind_param('i', $_GET['article_id']);
            $OK = $stmt->execute();
            $stmt->bind_result($cat_id);
            // loop through the results to store them in an array
            $selected_categories = [];
            while ($stmt->fetch()) {
                $selected_categories[] = $cat_id;
            }
        }
    }
}
// if form has been submitted, update record
if (isset($_POST ['update'])) {
    // prepare update query
    if (!empty($_POST['image_id'])) {
        $sql = 'UPDATE blog SET image_id = ?, title = ?, article = ?
                WHERE article_id = ?';
        if ($stmt->prepare($sql)) {
            $stmt->bind_param('issi', $_POST['image_id'], $_POST['title'],
                $_POST['article'], $_POST['article_id']);
            $done = $stmt->execute();
        }
    } else {
        $sql = 'UPDATE blog SET image_id = NULL, title = ?, article = ?
                WHERE article_id = ?';
        if ($stmt->prepare($sql)) {
            $stmt->bind_param('ssi', $_POST['title'], $_POST['article'],
                $_POST['article_id']);
            $done = $stmt->execute();
        }
    }
    // delete existing values in the cross-reference table
    $sql = 'DELETE FROM article2cat WHERE article_id = ?';
    if ($stmt->prepare($sql)) {
        $stmt->bind_param('i', $_POST['article_id']);
        $stmt->execute();
    }

    // insert the new values in articles2cat
    if (isset($_POST['category']) && is_numeric($_POST['article_id'])) {
        $article_id = (int) $_POST['article_id'];
        foreach ($_POST['category'] as $cat_id) {
            $values[] = "($article_id, " . (int) $cat_id . ')';
        }
        if ($values) {
            $sql = 'INSERT INTO article2cat (article_id, cat_id)
                    VALUES ' . implode(',', $values);
            if (!$conn->query($sql)) {
                $catError = $conn->error;
            }
        }
    }
}
// redirect page on success or if $_GET['article_id'] not defined
if ($done || !isset($_GET['article_id'])) {
    header('Location: http://localhost/phpsols/admin/blog_list_mysqli.php');
    exit;
}
// get error message if query fails
if (isset($stmt) && !$OK && !$done) {
    $error = $stmt->error;
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Update Blog Entry</title>
    <link href="../styles/admin.css" rel="stylesheet" type="text/css">
</head>

<body>
<h1>Update Blog Entry</h1>
<p><a href="blog_list_mysqli.php">List all entries </a></p>
<?php if (isset($error)) {
    echo "<p class='warning'>Error: $error</p>";
}
if($article_id == 0) { ?>
    <p class="warning">Invalid request: record does not exist.</p>
<?php } else { ?>
    <form method="post" action="">
        <p>
            <label for="title">Title:</label>
            <input name="title" type="text" id="title" value="<?= htmlentities($title); ?>">
        </p>
        <p>
            <label for="article">Article:</label>
            <textarea name="article" id="article"><?= htmlentities($article);?></textarea>
        </p>
        <p>
            <label for="category">Categories:</label>
            <select name="category[]" size="5" multiple id="category">
                <?php
                // get categories
                $getCats = 'SELECT cat_id, category FROM categories
                            ORDER BY category';
                $categories = $conn->query($getCats);
                while ($row = $categories->fetch_assoc()) {
                    ?>
                    <option value="<?= $row['cat_id']; ?>" <?php
                    if (isset($selected_categories) &&
                        in_array($row['cat_id'], $selected_categories)) {
                        echo 'selected';
                    } ?>><?= $row['category']; ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="image_id">Uploaded image:</label>
            <select name="image_id" id="image_id">
                <option value="">Select image</option>
                <?php
                // get the list images
                $getImages = 'SELECT image_id, filename
                              FROM images ORDER BY filename';
                $images = $conn->query($getImages);
                echo $conn->error;
                while ($row = $images->fetch_assoc()) {
                    ?>
                    <option value="<?= $row['image_id']; ?>"
                        <?php
                        if ($row['image_id'] == $image_id) {
                            echo 'selected';
                        }
                        ?>><?= $row['filename']; ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <input type="submit" name="update" value="Update Entry" id="update">
            <input name="article_id" type="hidden" value="<?= $article_id; ?>">
        </p>
    </form>
<?php } ?>
</body>
</html>