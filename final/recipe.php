<?php
require_once 'db.php';

// Function to get correct image path
function getCorrectImagePath($path) {
    // Trim any whitespace
    $path = trim($path);
    
    // If path is empty, return empty
    if (empty($path)) {
        return '';
    }
    
    // If path already has http:// or https://, return as is
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    // If path starts with 'recipes/', add leading slash
    if (strpos($path, 'recipes/') === 0) {
        return '/' . $path;
    }
    
    // If path doesn't start with /, add it
    if (strpos($path, '/') !== 0) {
        return '/' . $path;
    }
    
    return $path;
}

// Get recipe ID from URL
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($recipe_id <= 0) {
    // Redirect to homepage if no valid ID
    header('Location: index.php');
    exit();
}

// Connect to database
$conn = getDBConnection();

// Fetch recipe details
$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();

if (!$recipe) {
    // Recipe not found
    header('Location: index.php');
    exit();
}

$pageTitle = htmlspecialchars($recipe['name']);
$hideWelcome = true; // Hide welcome section on recipe pages

// Include header
require __DIR__ . '/partials/header.php';

// Parse data from text fields - FIXED: Database uses asterisks (*) not newlines
$ingredients = !empty($recipe['ingredients']) ? parseIngredients($recipe['ingredients']) : [];

// Parse tools - FIXED: Database stores tool name in 'tools' and description in 'tool_description'
$tools = !empty($recipe['tools']) ? parseTools($recipe['tools'], $recipe['tool_description'] ?? '') : [];

// Parse steps - FIXED: Database uses asterisks (*) to separate steps
$steps = !empty($recipe['steps']) ? parseSteps($recipe['steps']) : [];

// Process images from database - FIXED: Database uses asterisks (*) not commas
$rawImages = !empty($recipe['images']) ? explode('*', $recipe['images']) : [];
$images = [];

foreach ($rawImages as $img) {
    $correctedPath = getCorrectImagePath($img);
    if (!empty($correctedPath)) {
        $images[] = $correctedPath;
    }
}

// Debug: Check what we got
error_log("Recipe ID: " . $recipe_id);
error_log("Raw images string: " . $recipe['images']);
error_log("Processed images count: " . count($images));
foreach ($images as $i => $img) {
    error_log("Image $i: " . $img);
}

// If no valid images, use default
if (empty($images)) {
    $images = ['/resources/default.jpg'];
}

// Assign specific images for different sections
$mainImage = $images[0] ?? '';
$ingredientsImage = $images[1] ?? '';
// Tool image is typically at index 2
// Step images start at index 3 and go sequentially
?>

<div class="recipebody">
    <div class="cookingbody">
        <div class="textcenter">
            <h1><?= htmlspecialchars($recipe['name']) ?></h1>
            <p class="sub"><?= htmlspecialchars($recipe['subtitle'] ?? '') ?></p>
        </div>

        <?php if (!empty($mainImage)): ?>
        <img class="mainimg" src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($recipe['name']) ?>">
        <?php endif; ?>
    </div>

    <?php if (!empty($recipe['description'])): ?>
    <div class="txt">
        <p class="gry">Description</p>
        <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($ingredients)): ?>
    <div class="txt">
        <p class="gry">Ingredients</p>
        <div class="txtimg">
            <ul>
                <?php foreach ($ingredients as $ingredient): ?>
                <li><?= htmlspecialchars($ingredient) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if (!empty($ingredientsImage)): ?>
            <img src="<?= htmlspecialchars($ingredientsImage) ?>" alt="Ingredients">
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($tools)): ?>
    <div class="txt">
        <p class="gry">Tools</p>
        <?php 
        // Tool image is typically at index 2 in the images array
        $toolImageIndex = 2;
        $toolImage = $images[$toolImageIndex] ?? '';
        foreach ($tools as $tool): 
        ?>
        <div class="tools">
            <div class="toolsinfo">
                <h3><?= htmlspecialchars($tool['name']) ?></h3>
                <p><?= nl2br(htmlspecialchars($tool['description'])) ?></p>
            </div>
            <?php if (!empty($toolImage)): ?>
            <img src="<?= htmlspecialchars($toolImage) ?>" alt="<?= htmlspecialchars($tool['name']) ?>">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($steps)): ?>
    <div class="steps">
        <div class="txt"><p class="gry">Steps</p></div>
        <div class="all">
            <?php 
            $step_counter = 1;
            // Step images start at index 3
            $stepImageStartIndex = 3;
            
            foreach ($steps as $step): 
                $stepImageIndex = $stepImageStartIndex + ($step_counter - 1);
                $stepImage = $images[$stepImageIndex] ?? '';
            ?>
            <div class="stepsin">
                <h1 class="stepnumb">Step <?= $step_counter ?></h1>
                <div class="tools">
                    <div class="toolsinfo">
                        <?php if (!empty($step['title'])): ?>
                        <h3><?= htmlspecialchars($step['title']) ?></h3>
                        <?php endif; ?>
                        <p><?= nl2br(htmlspecialchars($step['description'])) ?></p>
                    </div>
                    <?php if (!empty($stepImage)): ?>
                    <img src="<?= htmlspecialchars($stepImage) ?>" alt="Step <?= $step_counter ?>">
                    <?php endif; ?>
                </div>
            </div>
            <?php 
                $step_counter++;
            endforeach; 
            ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($mainImage)): ?>
    <div class="enjoy">
        <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($recipe['name']) ?>">
        <h2>Enjoy!</h2>
    </div>
    <?php endif; ?>
</div>

<?php
// Include footer
require __DIR__ . '/partials/footer.php';

// Close database connection
$stmt->close();
$conn->close();

// HELPER FUNCTIONS

function parseIngredients($text) {
    // Database uses asterisks (*) to separate ingredients
    $items = explode('*', $text);
    $result = [];
    
    foreach ($items as $item) {
        $trimmed = trim($item);
        if (!empty($trimmed)) {
            $result[] = $trimmed;
        }
    }
    
    return $result;
}

function parseTools($toolsText, $toolDescription = '') {
    
    $toolNames = explode('*', $toolsText);
    $tools = [];
    
    foreach ($toolNames as $toolName) {
        $trimmed = trim($toolName);
        if (!empty($trimmed)) {
            $tool = [
                'name' => $trimmed,
                'description' => $toolDescription,
                'image' => '' 
            ];
            $tools[] = $tool;
        }
    }
    
    return $tools;
}

function parseSteps($stepsText) {
    
    $stepItems = explode('*', $stepsText);
    $steps = [];
    
    foreach ($stepItems as $stepItem) {
        $trimmed = trim($stepItem);
        if (!empty($trimmed)) {
            
            $title = '';
            $description = $trimmed;
            
            
            if (preg_match('/^Step\s+\d+:\s*(.+)/i', $trimmed, $matches)) {
                $title = 'Step ' . (count($steps) + 1);
                $description = $matches[1];
            }
            
            $step = [
                'title' => $title,
                'description' => $description,
                'image' => '' 
            ];
            $steps[] = $step;
        }
    }
    
    return $steps;
}
?>