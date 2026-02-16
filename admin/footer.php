<?php
/**
 * PROJECT: SkillLink
 * FILE: admin/footer.php
 * PURPOSE: Zero-Visual "Ghost" Footer for Zero-Scroll App Layout
 */
?>
    </section> 
</main>

<script>
    // Universal Command-K Listener (Brainstormed earlier)
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            document.querySelector('input[type="text"]').focus();
        }
    });
</script>

</body>
</html>