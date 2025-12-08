<footer>
    <hr>
    <h2 class="graytext">Enoch Tuffour</h2>
    <h2 class="graytext">&copy; <?= date('Y') ?></h2>
</footer>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script>
    const menuBtn = document.getElementById("menuBtn");
    const navRight = document.getElementById("rightmenu");

    if (menuBtn && navRight) {
        menuBtn.addEventListener("click", () => {
            navRight.classList.toggle("open");
        });
    }
</script>
</body>
</html>