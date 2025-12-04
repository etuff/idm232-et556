<?php
// recipe.php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = :id");
$stmt->execute([':id' => $id]);
$r = $stmt->fetch();

if (!$r) {
    http_response_code(404);
    echo "Recipe not found.";
    exit;
}

// parse images
$images = [];
if (!empty($r['images'])) {
    $s = $r['images'];
    if (strpos($s, '[') === 0) {
        $s2 = trim($s, "[] \t\n\r\0\x0B'\"");
        $parts = preg_split("/[,;]/", $s2);
        foreach ($parts as $p) {
            $p = trim($p, " '\"");
            if ($p) $images[] = $p;
        }
    } else {
        foreach (preg_split("/[,;]/", $s) as $p) {
            $p = trim($p, " '\"");
            if ($p) $images[] = $p;
        }
    }
}
if (empty($images)) $images[] = 'resources/welcome/default.jpg';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($r['name']); ?> — ReCipe</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="stylelist.css">
</head>
<body>
    <div class="results">
        <a href="index.php" style="color:var(--white); text-decoration:underline; margin-bottom:20px; display:inline-block;">← Back to recipes</a>
        <h1 style="color:var(--orange);"><?php echo htmlspecialchars($r['name']); ?></h1>
        <h3 style="color:var(--white)"><?php echo htmlspecialchars($r['subtitle']); ?></h3>

        <?php foreach ($images as $img): ?>
            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($r['name']); ?>" style="max-width:90%; margin:10px 0;">
        <?php endforeach; ?>

        <?php if (!empty($r['description'])): ?>
            <section style="max-width:900px; margin:20px auto; color:var(--white);">
                <h2>Description</h2>
                <p><?php echo nl2br(htmlspecialchars($r['description'])); ?></p>
            </section>
        <?php endif; ?>

        <?php if (!empty($r['ingredients'])): ?>
            <section style="max-width:900px; margin:20px auto; color:var(--white);">
                <h2>Ingredients</h2>
                <pre style="white-space:pre-wrap; background:transparent; color:var(--white); border:none; padding:0;"><?php echo htmlspecialchars($r['ingredients']); ?></pre>
            </section>
        <?php endif; ?>

        <?php if (!empty($r['steps'])): ?>
            <section style="max-width:900px; margin:20px auto; color:var(--white);">
                <h2>Steps</h2>
                <pre style="white-space:pre-wrap; background:transparent; color:var(--white); border:none; padding:0;"><?php echo htmlspecialchars($r['steps']); ?></pre>
            </section>
        <?php endif; ?>

    </div>

    <footer>
        <hr>
        <h2 class="graytext">Enoch Tuffour </h2>
        <h2 class="graytext">&copy; <?php echo date('Y'); ?></h2>
    </footer>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
