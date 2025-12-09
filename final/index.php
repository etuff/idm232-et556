<?php
require_once 'db.php';

$pageTitle = 'Home';
require __DIR__ . '/partials/header.php';

$conn = getDBConnection();

// Function to get image
function getCorrectImagePath($path) {
    
    $path = trim($path);
    
    
    if (empty($path)) {
        return '';
    }
    
    
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    
    if (strpos($path, 'recipes/') === 0) {
        return '/' . $path;
    }
    
    
    if (strpos($path, '/') !== 0) {
        return '/' . $path;
    }
    
    return $path;
}
?>

<div class="recipes">
    <h1 class="ttitle">Featured Recipes</h1>

    <div class="recipegrid">
        <?php 
        // Display all recipes
        $stmt = $conn->prepare("SELECT id, name, subtitle, images FROM recipes ORDER BY id DESC");
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            while($row = $res->fetch_assoc()): 
                
                $images = !empty($row['images']) ? explode('*', $row['images']) : [];
                
                
                $firstImage = !empty($images[0]) ? trim($images[0]) : 'resources/default.jpg';
                $image = getCorrectImagePath($firstImage);
                
                
        ?>
        <div class="recipebox">
            <a href="recipe.php?id=<?= $row['id'] ?>">
                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <div class="recipetext">
                    <h2><?= htmlspecialchars($row['name']) ?></h2>
                    <p><?= htmlspecialchars($row['subtitle'] ?? '') ?></p>
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