<div class='sidenav'>
    <div class='sidenav-image center'>
        <img src='assets/3leafs.png' />
    </div>
    <div>
        <div class='sidenav-header-wrapper'>
            <div class='sidenav-header'>
                Image boards
            </div>
        </div>
        <ul>
        <?php
            foreach ($boards as $key => $values) {
                echo '<li><a href="index.php?board/dir=' . $values['directory']  . '">' . $values['name'] . '</a></li>';
            }
        ?>
        </ul>
    </div>
</div>