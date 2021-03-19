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
    <div>
        <div class='sidenav-header-wrapper'>
            <div class='sidenav-header'>
                Options
            </div>
        </div>
        <ul>
            <li>
                <label class="theme-switch" for="dark-checkbox">
                    <input type='checkbox' id='dark-checkbox'>
                </label>
                <span class='sidenav-text'>Toggle dark mode</span>
                <script>
                    function switchTheme(e) {
                        if (e.target.checked) {
                            document.documentElement.setAttribute('data-theme', 'dark');
                            localStorage.setItem('theme', 'dark'); //this will be set to dark
                        }
                        else {
                            document.documentElement.setAttribute('data-theme', 'light');
                            localStorage.setItem('theme', 'light'); //this will be set to light
                        }
                    }
                    
                    const toggleSwitch = document.getElementById('dark-checkbox');
                    toggleSwitch.addEventListener('change', switchTheme, false);

                    const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;

                    if (currentTheme) {
                        document.documentElement.setAttribute('data-theme', currentTheme);

                        if (currentTheme === 'dark') {
                            toggleSwitch.checked = true;
                        }
                    }
                </script>
            </li>
        </ul>
    </div>
</div>