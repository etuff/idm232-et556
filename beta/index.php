<!DOCTYPE html>
<html>

<head>
  <title>ReCipe — PHP</title>
  <link rel="stylesheet" href="./styles/general.css">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="">
  <html lang="en">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@300&family=Nova+Script&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@200;300&family=Nova+Script&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="./images/smallBannerLogo.png">
  <style>
    /* small override to ensure images in grid shrink nicely */
    .recipebox img { width:100%; height:auto; display:block; }
    .recipegrid { justify-items:center; }
  </style>
</head>

<body>

  <?php
    // Include the database connection code
    require_once './includes/database.php';
    
    // Include env.php that holds global vars with secret info
    require_once './env.php';
    
    require_once './includes/fun.php';
    consoleMsg("PHP to JS .. is Wicked FUN");

    // Optional search query
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    consoleMsg("Search string is: $q");

    // Pagination variables
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 12;
    $offset = ($page - 1) * $perPage;

    $where = '';
    $params = [];

    if ($q !== '') {
        $where = "WHERE Title LIKE :q OR Subtitle LIKE :q";
        $params[':q'] = "%$q%";
    }

    // Count total recipes
    $countQuery = "SELECT COUNT(*) FROM recipes $where";
    $countStmt = $db_connection->prepare($countQuery);
    foreach ($params as $k => $v) {
        $countStmt->bindValue($k, $v);
    }
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();
    $totalPages = max(1, ceil($total / $perPage));
    consoleMsg("Total recipes: $total, Total pages: $totalPages");

    // Fetch recipes for current page
    $query = "SELECT * FROM recipes $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $db_connection->prepare($query);
    
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    
    $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $recipes = $stmt->fetchAll();

    // Helper function for search value
    function echoSearchValue() {
        global $q;
        if (!empty($q)) {
            echo htmlspecialchars($q);
        }
    }
  ?>

  <!-- Echo commands for header and navigation -->
  <header>
    <?php
      echo "<a class='logo' href='index.php'><img class='logo' src='./images/smallBannerLogo.png' alt='logo'></a>";
      echo "<nav class='sample'>";
      echo "<ul class='nav_links'>";
      echo "<li>"; echo "<a href='index.php'>ALL RECIPES</a>"; echo "</li>";
      echo "</nav>";
      echo "<button class='hamburger'>";
        echo "<div class='bar'></div>";
      echo "</button>";
    ?>
  </header>

  <!-- Mobile hamburger menu -->
  <nav class="mobile-nav">
    <?php
      echo "<a href='index.php'>ALL RECIPES</a>";
      echo "<a href='help.html'>HELP</a>";
    ?>
  </nav>

  <!-- Container for hero image and search -->
  <div class="img-container">
      <!-- Hero image div -->
      <div class="inner-container">
        <?php
          echo "<img class='heroImg' src='./images/longBannerLogo.png' alt='header logo'>";
          echo "<h1 class='cursive' style='text-align:center;font-size:3rem;margin:10px 0;'>ReCipe</h1>";
          echo "<p class='heroSubtitle'>Where Flavor Takes Center Stage &dash; Savor the Culinary Journey!</p>";
        ?>

        <!-- Search form -->
        <div class="searchbar">
          <form method="get" action="index.php" style="display:flex;align-items:center;justify-content:center;">
            <input class="input" type="text" name="q" placeholder="Search..." value="<?php echo htmlspecialchars($q); ?>"/>
            <button class="btn" type="submit">
              <img src="./images/search.png" alt="search button" style="width:20px;height:20px;">
            </button>
          </form>
        </div>
      </div>
  </div>

  <!-- Thumbnail image header -->
  <?php
    echo "<p class='thumbnailHeader'>Featured Recipes</p>";
  ?>

  <!-- Thumbnail container -->
  <div class="thumbnailContainer recipegrid">
        <?php
            if (count($recipes) === 0) {
                echo '<div>';
                echo "<img class='errorImg' src='./images/errorImg.png' alt='No recipes found'>";
                echo '<p class="errorMsg">No recipes found';
                if (!empty($q)) {
                    echo ' for "' . htmlspecialchars($q) . '"';
                }
                echo '. Please try searching again!</p>';
                echo '</div>';
            } else {
                foreach ($recipes as $r): 
                    // Get image from Main IMG column
                    $img = '';
                    if (!empty($r['Main IMG'])) {
                        $img = './images/' . $r['Main IMG'];
                    } else {
                        $img = './images/default.jpg';
                    }
        ?>
            <div class="recipebox">
                <a href="./indexRecipe.php?recID=<?php echo $r['id']; ?>">
                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($r['Title']); ?>">
                    <div class="recipetext">
                        <h2><?php echo htmlspecialchars($r['Title']); ?></h2>
                        <p><?php echo htmlspecialchars($r['Subtitle']); ?></p>
                    </div>
                </a>
            </div>
        <?php 
                endforeach; 
            }
        ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div style="margin-top:30px; text-align:center; padding:20px;">
    <?php if ($page > 1): ?>
      <a href="index.php?q=<?php echo urlencode($q); ?>&page=<?php echo $page-1; ?>" class="btn" style="margin:0 5px;">Prev</a>
    <?php endif; ?>
    <span style="color:#333; margin:0 10px;">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
      <a href="index.php?q=<?php echo urlencode($q); ?>&page=<?php echo $page+1; ?>" class="btn" style="margin:0 5px;">Next</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- Footer echo -->
  <footer>
    <?php
      echo "<p>Copyright  &copy;" . date('Y') . " Savor + Sizzle</p>";
      echo "<p>Enoch Tuffour</p>";
    ?>
  </footer>

  <!-- Scripts -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="./scripts/script.js"></script>
</body>
</html>