<?php
// index.php
require 'db.php';

// Optional search query
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Pagination variables (simple)
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];

if ($q !== '') {
    $where = "WHERE name LIKE :q OR subtitle LIKE :q OR description LIKE :q";
    $params[':q'] = "%$q%";
}

// Count total
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM recipes $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

// Fetch items
$listStmt = $pdo->prepare("SELECT * FROM recipes $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) {
    $listStmt->bindValue($k, $v);
}
$listStmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$listStmt->execute();
$recipes = $listStmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ReCipe — PHP</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="stylelist.css">
    <style>
      /* small override to ensure images in grid shrink nicely */
      .recipebox img { width:100%; height:auto; display:block; }
      .recipegrid { justify-items:center; }
    </style>
</head>
<body>
    <div class="welcome">
        <header>
            <div class="toprow">
                <h1 class="cursive"><a href="index.php">ReCipe</a></h1>
                <ion-icon name="menu-outline" id="menuBtn"></ion-icon>
            </div>

            <div id="rightmenu">
                <h2><a href="help.html">Help</a></h2>
                <div class="searchbar">
                    <form method="get" action="index.php" style="display:flex;align-items:center;">
                        <input type="text" name="q" placeholder="Search..." value="<?php echo htmlspecialchars($q); ?>"/>
                        <button type="submit">
                            <img src="resources/welcome/search.png" alt="search button">
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div id="welcomecenter">
            <div class="welcometxt">
                <h1 class="cursive">ReCipe</h1>
                <div class="searchbar">
                    <form method="get" action="index.php" style="display:flex;align-items:center;">
                        <input type="text" name="q" placeholder="Search..." value="<?php echo htmlspecialchars($q); ?>"/>
                        <button type="submit">
                            <img src="resources/welcome/search.png" alt="search button">
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="recipes">
        <h1 class="ttitle">Featured Recipes</h1>

        <div class="recipegrid">
            <?php if (count($recipes) === 0): ?>
                <p style="color:white">No recipes found.</p>
            <?php endif; ?>

            <?php foreach ($recipes as $r): 
                // images field might be a list; attempt to parse first image out
                $img = '';
                if (!empty($r['images'])) {
                    // CSV stored images as something like "['path1','path2']" or comma separated
                    // Try to find the first URL-like token
                    if (strpos($r['images'], '[') === 0) {
                        // strip brackets and quotes
                        $str = trim($r['images'], "[] \t\n\r\0\x0B'\"");
                        $parts = preg_split("/[,;]/", $str);
                        $imgCandidate = trim($parts[0], " '\"");
                        $img = $imgCandidate;
                    } else {
                        // comma separated
                        $parts = preg_split("/[,;]/", $r['images']);
                        $img = trim($parts[0], " '\"");
                    }
                }
                // fallback image
                if ($img === '') $img = 'resources/welcome/default.jpg';
            ?>
            <div class="recipebox">
                <a href="recipe.php?id=<?php echo $r['id']; ?>">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($r['name']); ?>">
                    <div class="recipetext">
                        <h2><?php echo htmlspecialchars($r['name']); ?></h2>
                        <p><?php echo htmlspecialchars($r['subtitle']); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- pagination -->
        <div style="margin-top:30px; text-align:center;">
            <?php if ($page > 1): ?>
                <a href="index.php?q=<?php echo urlencode($q); ?>&page=<?php echo $page-1; ?>">Prev</a>
            <?php endif; ?>
            <span style="color:white; margin:0 10px;">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="index.php?q=<?php echo urlencode($q); ?>&page=<?php echo $page+1; ?>">Next</a>
            <?php endif; ?>
        </div>

    </div>

    <footer>
        <hr>
        <h2 class="graytext">Enoch Tuffour </h2>
        <h2 class="graytext">&copy; <?php echo date('Y'); ?></h2>
    </footer>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        const menuBtn = document.getElementById("menuBtn");
        const navRight = document.getElementById("rightmenu");
        menuBtn.addEventListener("click", () => {
            navRight.classList.toggle("open");
        });
    </script>
</body>
</html>
