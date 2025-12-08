<?php
require_once 'db.php';

$pageTitle = 'Home';
require __DIR__ . '/partials/header.php';

$conn = getDBConnection();
?>

<div class="recipes">
    <h1 class="ttitle">Featured Recipes</h1>

    <div class="recipegrid">
        <?php 
        // display all recipe
        $stmt = $conn->prepare("SELECT id, name, subtitle, images FROM recipes ORDER BY id DESC");
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            while($row = $res->fetch_assoc()): 
                $images = explode(',', $row['images']);
                // FIX: Add leading slash to image paths
                $image = !empty($images[0]) ? '/' . ltrim($images[0], '/') : '/resources/default.jpg';
        ?>
        <div class="recipebox">
            <a href="recipe.php?id=<?= $row['id'] ?>">
                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <div class="recipetext">
                    <h2><?= htmlspecialchars($row['name']) ?></h2>
                    <p><?= htmlspecialchars($row['subtitle']) ?></p>
                </div>
            </a>
        </div>
        <?php 
            endwhile;
        } else {
           
        ?>
        <div class="no-results-container">
            <img src="/resources/nosearch.png" alt="No recipes available" class="no-results-image">
            <p class="no-results-message">No recipes available at the moment.</p>
        </div>
        <?php
        }
        
        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; 

?>