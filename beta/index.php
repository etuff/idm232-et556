<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe</title>
    <link rel="stylesheet" href="stylelist.css">
</head>
<body>

<div class="welcome">
    <header>
        <div class="toprow">
            <h1 class="cursive"><a href="index.php">ReCipe</a></h1>
            <ion-icon name="menu-outline" id="menuBtn"></ion-icon>
        </div>

        <div id="rightmenu">
            <h2><a href="help.php">Help</a></h2>

            <form class="searchbar" action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search..." required />
                <button type="submit">
                    <img src="resources/welcome/search.png" alt="search button">
                </button>
            </form>
        </div>
    </header>

    <div id="welcomecenter">
        <div class="welcometxt">
            <h1 class="cursive">ReCipe</h1>

            <form class="searchbar" action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search..." required />
                <button type="submit">
                    <img src="resources/welcome/search.png" alt="search button">
                </button>
            </form>
        </div>
    </div>
</div>

<br>

<div class="recipes">
    <h1 class="ttitle">Featured Recipes</h1>

    <div class="recipegrid">
        <?php
        require_once "db.php";
        $sql = "SELECT id, title, subtitle, image FROM recipes LIMIT 12";
        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()):
        ?>
        <div class="recipebox">
            <a href="recipe.php?id=<?= $row['id'] ?>">
                <img src="<?= $row['image'] ?>" alt="<?= $row['title'] ?>">
                <div class="recipetext">
                    <h2><?= $row['title'] ?></h2>
                    <p><?= $row['subtitle'] ?></p>
                </div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<footer>
    <hr>
    <h2 class="graytext">Enoch Tuffour</h2>
    <h2 class="graytext">&copy; 2025</h2>
</footer>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script>
    document.getElementById("menuBtn").addEventListener("click", () => {
        document.getElementById("rightmenu").classList.toggle("open");
    });
</script>

</body>
</html>
