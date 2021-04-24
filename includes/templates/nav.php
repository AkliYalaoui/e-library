<header class="header-nav">
    <nav class="nav-bar">
        <h2 class="nav-title"><a href="index.php">Library.<span>fr</span></a></h2>
        <ul class="nav-links">
            <li>
                <a href="index.php" class="<?php echo  str_contains($_SERVER['REQUEST_URI'],'index.php') ? 'active':''; ?>">Home<i class="fa fa-home"></i></a>
            </li>
            <li>
                <a href="">on loan<i class="fa fa-book-dead"></i></a>
            </li>
            <li>
                <a href="">all books<i class="fa fa-book-reader"></i></a>
            </li>
            <li>
                <a href="">Books management<i class="fa fa-book"></i></a>
            </li>
            <li>
                <a href="">Users management<i class="fa fa-user-friends"></i></a>
            </li>
            <li class="sub-nav">
                <button id="sub-nav-button">Akli Yalaoui<i class="fa fa-arrow-alt-circle-down"></i></button>
                <ul id="sub-nav-menu">
                    <li><a href="">Profile<i class="fa fa-user-circle"></i></a></li>
                    <li><a href="logout.php">Logout <i class="fa fa-sign-out-alt"></i></a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
