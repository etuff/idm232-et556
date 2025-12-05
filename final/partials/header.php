<?php
// partials/header.php
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle ?? 'ReCipe') ?></title>
  <link rel="stylesheet" href="/stylelist.css" />
</head>
<body>
<header>
  <div class="toprow">
    <h1 class="cursive"><a href="/index.php">ReCipe</a></h1>
    <button id="menuBtn" aria-label="menu">☰</button>
  </div>
  <nav id="rightmenu">
    <a href="/help.php">Help</a>
    <form action="/search.php" method="get" class="searchbar">
      <input name="q" type="search" placeholder="Search..." aria-label="Search" required />
      <button type="submit">🔍</button>
    </form>
  </nav>
</header>
<main>
