<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe - <?= htmlspecialchars($pageTitle ?? 'Home') ?></title>
    <link rel="stylesheet" href="stylelist.css">
    
    <?php if (isset($hideWelcome) && $hideWelcome === true): ?>
        
        <link rel="stylesheet" href="recipestyle.css">
    <?php endif; ?>
</head>
<body>

<?php if (!isset($hideWelcome) || $hideWelcome !== true): ?>
<div class="welcome">
    <header>
        <div class="toprow">
            <h1 class="cursive"><a href="index.php">ReCipe</a></h1>
            <ion-icon name="menu-outline" id="menuBtn"></ion-icon>
        </div>

        <div id="rightmenu">
            <form action="search.php" method="GET" class="searchbar">
                <input type="text" name="q" placeholder="Search..." 
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit">
                    <img src="resources/welcome/search.png" alt="search button">
                </button>
            </form>
        </div>
    </header>

    <div id="welcomecenter">
        <div class="welcometxt">
            <h1 class="cursive">ReCipe</h1>
            <form action="search.php" method="GET" class="searchbar">
                <input type="text" name="q" placeholder="Search..." 
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit">
                    <img src="resources/welcome/search.png" alt="search button">
                </button>
            </form>
        </div>
    </div>
</div>
<br>
<?php else: ?>

<header class="recipe-header">
    <div class="toprow">
        <h1 class="cursive"><a href="index.php">ReCipe</a></h1>
        <ion-icon name="menu-outline" id="menuBtn"></ion-icon>
    </div>

    <div id="rightmenu">
        <form action="search.php" method="GET" class="searchbar">
            <input type="text" name="q" placeholder="Search..." 
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit">
                <img src="resources/welcome/search.png" alt="search button">
            </button>
        </form>
    </div>
</header>
<?php endif; ?>