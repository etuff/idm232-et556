<?php
require_once 'db.php';

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
$tools = !empty($recipe['tools']) ? parseTools($recipe['tools']) : [];
$steps = !empty($recipe['steps']) ? parseSteps($recipe['steps']) : [];

// FIX: Add leading slash to all image paths
$images = !empty($recipe['images']) ? explode(',', $recipe['images']) : ['/resources/default.jpg'];
foreach ($images as &$img) {
    $img = '/' . ltrim(trim($img), '/');
}
?>

<div class="recipebody">
    <div class="cookingbody">
        <div class="textcenter">
            <h1><?= htmlspecialchars($recipe['name']) ?></h1>
            <p class="sub"><?= htmlspecialchars($recipe['subtitle']) ?></p>
        </div>

        <img class="mainimg" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($recipe['name']) ?>">
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

    <div class="enjoy">
        <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($recipe['name']) ?>">
        <h2>Enjoy!</h2>
    </div>
</div>

<?php
// Include footer
require __DIR__ . '/partials/footer.php';

// Close database connection
$stmt->close();
$conn->close();

// HELPER FUNCTIONS with image path fixes
function parseTextToArray($text) {
    // Split by new lines and remove empty lines
    $lines = explode("\n", $text);
    $result = [];
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (!empty($trimmed)) {
            $result[] = $trimmed;
        }
    }
    
    return $result;
}

function parseTools($toolsText) {
    // Format: TOOL_NAME|TOOL_DESCRIPTION|TOOL_IMAGE
    $lines = explode("\n", $toolsText);
    $tools = [];
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (!empty($trimmed)) {
            $parts = explode('|', $trimmed);
            if (count($parts) >= 2) {
                $tool = [
                    'name' => $parts[0],
                    'description' => $parts[1],
                    'image' => !empty($parts[2]) ? '/' . ltrim(trim($parts[2]), '/') : ''
                ];
                $tools[] = $tool;
            }
        }
    }
    
    return $tools;
}

function parseSteps($stepsText) {
    // Format: STEP_TITLE|STEP_DESCRIPTION|STEP_IMAGE
    // Title can be empty
    $lines = explode("\n", $stepsText);
    $steps = [];
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (!empty($trimmed)) {
            $parts = explode('|', $trimmed);
            if (count($parts) >= 2) {
                $step = [
                    'title' => $parts[0] ?? '',
                    'description' => $parts[1],
                    'image' => !empty($parts[2]) ? '/' . ltrim(trim($parts[2]), '/') : ''
                ];
                $steps[] = $step;
            }
        }
    }
    
    return $steps;
}
?>