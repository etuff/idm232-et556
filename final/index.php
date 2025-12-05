<?php
require_once 'db.php';
$pageTitle = 'Home';
require __DIR__ . '/partials/header.php';

$stmt = $conn->prepare("SELECT id, name, subtitle, images FROM recipes ORDER BY created_at DESC");
$stmt->execute();
$res = $stmt->get_result();
?>
<section class="recipes">
  <h1 class="ttitle">Featured Recipes</h1>
  <div class="recipegrid">
    <?php while($row = $res->fetch_assoc()): 
      $image = explode(',', $row['images'])[0] ?? 'resources/default.jpg';
    ?>
    <article class="recipebox">
      <a href="recipe.php?id=<?= $row['id'] ?>">
        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
        <div class="recipetext">
          <h2><?= htmlspecialchars($row['name']) ?></h2>
          <p><?= htmlspecialchars($row['subtitle']) ?></p>
        </div>
      </a>
    </article>
    <?php endwhile; ?>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
