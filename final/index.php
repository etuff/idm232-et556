<?php
require_once 'db.php';

$pageTitle = 'Home';
require __DIR__ . '/partials/header.php';

$conn = getDBConnection();

// Function to get correct image path
function getCorrectImagePath($path) {
    // Trim any whitespace
    $path = trim($path);
    
    // If path already has http:// or https://, return as is
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    // If path starts with 'recipes/', add leading slash
    if (strpos($path, 'recipes/') === 0) {
        return '/' . $path;
    }
    
    // If path doesn't start with /, add it
    if (!empty($path) && strpos($path, '/') !== 0) {
        return '/' . $path;
    }
    
    // Default fallback
    if (empty($path)) {
        return '/resources/default.jpg';
    }
    
    return $path;
}
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
                // Get the first image or default
                $firstImage = !empty($images[0]) ? trim($images[0]) : 'resources/default.jpg';
                $image = getCorrectImagePath($firstImage);
                
                // DEBUG: Show the actual path being used
                // echo "<!-- DEBUG: Image path for '{$row['name']}': $image -->";
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

<?php require __DIR__ . '/partials/footer.php'; ?>