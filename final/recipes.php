<?php
require_once "db.php";

$q = $_GET['q'] ?? '';

$stmt = $conn->prepare("SELECT id, title, subtitle, image FROM recipes WHERE title LIKE ? OR subtitle LIKE ? OR ingredients LIKE ?");
$like = "%$q%";
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$results = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
    <link rel="stylesheet" href="stylelist.css">
</head>
<body>

<h1>Results for "<?= htmlspecialchars($q) ?>"</h1>

<div class="recipegrid">
<?php while($row = $results->fetch_assoc()): ?>
    <div class="recipebox">
        <a href="recipe.php?id=<?= $row['id'] ?>">
            <img src="<?= $row['image'] ?>" alt="<?= $row['title'] ?>">
            <div class="recipetext">
                <h2><?= $row['title'] ?></h2>
                <p><?= $row['subtitle'] ?></p>
            </div>
        </a>
    </div>
<?php endwhile; ?>
</div>

</body>
</html>
