<header class="header-nav">
    <nav class="nav-bar">
       <div class="nav-mobile">
           <h2 class="nav-title"><a href="<?php echo $navLinks['home'] ?>">Library.<span>fr</span></a></h2>
           <button id="nav-button"><i class="fa fa-bars fa-2x"></i></button>
       </div>
        <ul class="nav-links" id="nav-menu">
            <li>
                <a href="<?php echo $navLinks['home'] ?>">Home<i class="fa fa-home"></i></a>
            </li>
            <?php if($_SESSION['is_active'] == 0): ?>
                <li>
                    <a href="<?php echo $navLinks['loan'] ?>">on loan<i class="fa fa-book-dead"></i></a>
                </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo $navLinks['book'] ?>">all books<i class="fa fa-book-reader"></i></a>
            </li>
            <?php if($_SESSION['is_admin'] == 0): ?>
                <li>
                    <a href="<?php echo $navLinks['admin_book'] ?>">Books management<i class="fa fa-book"></i></a>
                </li>
                <li>
                    <a href="<?php echo $navLinks['admin_user'] ?>">Users management<i class="fa fa-user-friends"></i></a>
                </li>
            <?php endif; ?>
            <li class="sub-nav">
                <button id="sub-nav-button"><?php echo $_SESSION['name']?><i class="fa fa-arrow-alt-circle-down"></i></button>
                <ul id="sub-nav-menu">
                    <li><a href="<?php echo $navLinks['profile'] ?>">Profile<i class="fa fa-user-circle"></i></a></li>
                    <li><a href="<?php echo $navLinks['logout'] ?>">Logout <i class="fa fa-sign-out-alt"></i></a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<?php if($_SESSION['is_active'] == 1):?>
<div class="modal" id="modal">
    <div>
        <i class="fa fa-bell fa-5x"></i>
    </div>
    <p>Your account is waiting for admin approval</p>
    <button id="closeModal">Got it!</button>
</div>
<?php endif; ?>