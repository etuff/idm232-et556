<?php
require_once 'db.php';

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
    
    // Default fallback or empty for optional images
    if (empty($path)) {
        return '';
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

// Parse data from text fields
$ingredients = !empty($recipe['ingredients']) ? parseTextToArray($recipe['ingredients']) : [];

// Parse tools - using both tools and tool_description fields
$tools = [];
if (!empty($recipe['tools'])) {
    $tools = parseTools($recipe['tools'], $recipe['tool_description'] ?? '');
}

// Parse steps - database uses asterisks to separate steps
$steps = !empty($recipe['steps']) ? parseSteps($recipe['steps']) : [];

// Process images from database
$rawImages = !empty($recipe['images']) ? explode('*', $recipe['images']) : [];
$images = [];
foreach ($rawImages as $img) {
    $correctedPath = getCorrectImagePath($img);
    if (!empty($correctedPath)) {
        $images[] = $correctedPath;
    }
}

// If no valid images, use default
if (empty($images)) {
    $images = ['/resources/default.jpg'];
}

// DEBUG: Show what we're working with
// echo "<!-- DEBUG: Raw images from DB: " . htmlspecialchars($recipe['images']) . " -->";
// echo "<!-- DEBUG: Processed images: " . implode(', ', $images) . " -->";
?>

<div class="recipebody">
    <div class="cookingbody">
        <div class="textcenter">
            <h1><?= htmlspecialchars($recipe['name']) ?></h1>
            <p class="sub"><?= htmlspecialchars($recipe['subtitle']) ?></p>
        </div>

        <?php if (!empty($images[0])): ?>
        <img class="mainimg" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($recipe['name']) ?>">
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
            <?php if (isset($images[1])): ?>
            <img src="<?= htmlspecialchars($images[1]) ?>" alt="Ingredients">
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($tools)): ?>
    <div class="txt">
        <p class="gry">Tools</p>
        <?php foreach ($tools as $tool): ?>
        <div class="tools">
            <div class="toolsinfo">
                <h3><?= htmlspecialchars($tool['name']) ?></h3>
                <p><?= nl2br(htmlspecialchars($tool['description'])) ?></p>
            </div>
            <?php if (!empty($tool['image'])): ?>
            <img src="<?= htmlspecialchars($tool['image']) ?>" alt="<?= htmlspecialchars($tool['name']) ?>">
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
            foreach ($steps as $step): 
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
                    <?php if (!empty($step['image'])): ?>
                    <img src="<?= htmlspecialchars($step['image']) ?>" alt="Step <?= $step_counter ?>">
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

    <?php if (!empty($images[0])): ?>
    <div class="enjoy">
        <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($recipe['name']) ?>">
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
function parseTextToArray($text) {
    // Split by asterisk (database format) and remove empty lines
    $lines = explode('*', $text);
    $result = [];
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (!empty($trimmed)) {
            $result[] = $trimmed;
        }
    }
    
    return $result;
}

function parseTools($toolsText, $toolDescription = '') {
    // Tools are stored simply in the database
    // Some recipes might have multiple tools separated by asterisk
    $tools = [];
    
    // Split by asterisk if multiple tools exist
    $toolNames = explode('*', $toolsText);
    
    foreach ($toolNames as $toolName) {
        $trimmed = trim($toolName);
        if (!empty($trimmed)) {
            $tool = [
                'name' => $trimmed,
                'description' => $toolDescription, // Use the separate tool_description field
                'image' => '' // Tool images are in the images field, not with tool data
            ];
            $tools[] = $tool;
        }
    }
    
    return $tools;
}

function parseSteps($stepsText) {
    // Database uses asterisks to separate steps
    $steps = [];
    
    // Split steps by asterisk
    $stepItems = explode('*', $stepsText);
    
    foreach ($stepItems as $stepItem) {
        $trimmed = trim($stepItem);
        if (!empty($trimmed)) {
            $step = [
                'title' => '', // Steps don't have separate titles in database
                'description' => $trimmed,
                'image' => '' // Step images are in the images field
            ];
            $steps[] = $step;
        }
    }
    
    return $steps;
}
?>