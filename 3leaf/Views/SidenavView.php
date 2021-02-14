<div class='sidenav'>
    <?php
        foreach ($boards as $key => $values) {
            echo '<a href="#">' . $values['name'] . '</a>';
        }
    ?>
</div>