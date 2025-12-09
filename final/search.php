<?php
require_once 'db.php';

$searchTerm = $_GET['q'] ?? '';
$pageTitle = 'Search Results';

// Include header
require __DIR__ . '/partials/header.php';

// Database connection
$conn = getDBConnection();


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
    <h1 class="ttitle">
        <?= !empty($searchTerm) 
            ? "Search Results for: " . htmlspecialchars($searchTerm) 
            : "Search Recipes" ?>
    </h1>

    <div class="recipegrid">
        <?php 
        if (!empty($searchTerm)) {
            // Fetch search results - only searching name and subtitle columns
            $stmt = $conn->prepare("
                SELECT id, name, subtitle, images 
                FROM recipes 
                WHERE name LIKE ? OR subtitle LIKE ?
                ORDER BY id DESC
            ");
            $searchPattern = "%" . $searchTerm . "%";
            $stmt->bind_param("ss", $searchPattern, $searchPattern);
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
                // No results found
        ?>
        <div class="no-results-container" style="background: none; border: none; box-shadow: none;">
            <img src="/resources/nosearch.png" alt="No results found" class="no-results-image">
            <p class="no-results-message">No recipes found for "<?= htmlspecialchars($searchTerm) ?>". Try a different search term.</p>
        </div>
        <?php
            }
            $stmt->close();
        } else {
            
        ?>
        <div class="no-results-container" style="background: none; border: none; box-shadow: none;">
            <img src="/resources/nosearch.png" alt="Enter search term" class="no-results-image">
            <p class="no-results-message">Please enter a search term to find recipes.</p>
        </div>
        <?php
        }
        
        $conn->close();
        ?>
    </div>
</div>

<?php

// Include footer
require __DIR__ . '/partials/footer.php';
?>