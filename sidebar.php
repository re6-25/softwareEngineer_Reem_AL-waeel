<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current = basename($_SERVER['PHP_SELF']);
?>
<style>
.header-main {
    width: 100%;
    background: #fff;
    box-shadow: 0 2px 28px #d7cbfa20;
    border-bottom: 2px solid #f4f0fb;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 32px 0 10px;
    min-height: 74px;
    position: sticky;
    top: 0;
    z-index: 110;
}
.header-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 2rem;
    font-weight: bold;
    color: #7157c7;
    letter-spacing: 2px;
}
.header-logo i { color: #f9c846; font-size: 1.4em; }
.header-search {
    flex: 1;
    display: flex;
    align-items: center;
    margin: 0 24px;
}
.header-search input[type="text"] {
    width: 330px;
    max-width: 58vw;
    border-radius: 14px;
    border: 1.4px solid #ececec;
    background: #fafbfe;
    padding: 9px 18px;
    font-size: 1.07em;
    transition: border 0.2s;
}
.header-search input[type="text"]:focus { border-color: #a390e4; outline: none; }
.header-nav {
    display: flex;
    align-items: center;
    gap: 9px;
}
.header-nav a {
    color: #393e46;
    text-decoration: none;
    font-size: 1.07em;
    font-weight: 600;
    padding: 8px 15px;
    border-radius: 10px;
    transition: background 0.13s, color 0.13s;
}
.header-nav a.active, .header-nav a:hover {
    background: linear-gradient(90deg, #a390e4 70%, #f4f7fa 100%);
    color: #fff !important;
}
.header-user {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-right: 18px;
}
.header-user-img {
    width: 38px; height: 38px;
    border-radius: 50%;
    border: 2px solid #a390e4;
    object-fit: cover;
    background: #f8f9fa;
    box-shadow: 0 2px 6px #b7a5e733;
}
.header-btn {
    background: #a390e4;
    color: #fff !important;
    border: none;
    border-radius: 13px;
    font-weight: bold;
    padding: 9px 25px;
    font-size: 1.06em;
    margin-right: 10px;
    cursor: pointer;
    box-shadow: 0 2px 12px #a390e422;
    transition: background 0.14s;
}
.header-btn:hover { background: #7157c7; }
.header-icons {
    display: flex;
    gap: 11px;
    align-items: center;
    margin-left: 11px;
}
.header-icons a {
    color: #a390e4;
    font-size: 1.33em;
    transition: color 0.16s;
}
.header-logo .header-user-img{
    width: 150px;
    height: 60px;
      object-fit:contain;
}
.header-icons a:hover { color: #f9c846; }
@media (max-width: 991px) {
    .header-main { flex-wrap: wrap; padding: 0 8px;}
    .header-search input[type="text"] { width: 140px; max-width: 46vw; }
    .header-logo { font-size: 1.3em; }
    .header-user-img { width: 30px; height: 30px;}
    .header-nav a { padding: 6px 10px; font-size: .96em;}
}
@media (max-width: 600px) {
    .header-main { min-height: 56px;}
    .header-logo { font-size: 1em;}
    .header-search input[type="text"] { width: 78px; font-size: .91em;}
    .header-user { margin-right: 3px; }
    .header-btn { padding: 6px 11px; font-size: .98em; }
}
@media (max-width: 900px) {
    .header-main { flex-direction: column; padding: 0 3vw; min-height: 50px; }
    .header-logo { margin: 8px 0; font-size: 1em; }
    .header-nav { flex-wrap: wrap; gap: 4px; justify-content: center; }
    .header-nav a { padding: 5px 6px; font-size: .95em; }
    .header-search { margin: 5px 0 0 0; width: 100%; justify-content: center; }
    .header-search input[type="text"] { width: 100px; max-width: 40vw; font-size: .98em; }
    .header-icons { margin-left: 3px; gap: 4px;}
    .header-user { margin-right: 0; gap: 3px;}
    .header-user-img { width: 26px; height: 26px;}
    .header-btn { padding: 6px 9px; font-size: .91em;}
}

@media (max-width: 600px) {
    .header-main { flex-direction: column; padding: 0 1vw; min-height: 35px; }
    .header-logo { font-size: .85em; }
    .header-nav a { padding: 4px 4px; font-size: .85em; margin: 1px 0; }
    .header-search input[type="text"] { width: 66px; font-size: .89em;}
    .header-user span { display: none; }
    .header-user-img { width: 21px; height: 21px;}
    .header-btn { padding: 5px 8px; font-size: .8em; margin-right: 0;}
    .header-icons a { font-size: 1em;}
}

@media (max-width: 400px) {
    .header-main { flex-direction: column; padding: 0 2px; min-height: 25px;}
    .header-nav { gap: 1px; }
    .header-nav a { font-size: .7em; padding: 2px 2px;}
    .header-logo { font-size: .65em;}
    .header-search input[type="text"] { width: 38px; font-size: .65em;}
    .header-icons { gap: 1px;}
}

</style>
<div class="header-main">
    <div class="header-logo">
         <img class ="header-user-img" src="uploads\OIP (4).jpg" alt="book">
        <i></i> الإلهام الساكن
    </div>
    <div class="header-nav">
        <a href="index.php" class="<?= $current == 'index.php' ? 'active' : '' ?>">الرئيسية</a>
        <a href="categories.php" class="<?= $current == 'categories.php' ? 'active' : '' ?>">التصنيفات</a>
        <a href="blogs.php" class="<?= $current == 'blogs.php' ? 'active' : '' ?>">المدونات</a>
        <a href="favorites.php" class="<?= $current == 'favorites.php' ? 'active' : '' ?>">المفضلة</a>
        <a href="cart.php" class="<?= $current == 'cart.php' ? 'active' : '' ?>">السلة</a>
        <a href="game.php" class="<?= $current == 'game.php' ? 'active' : '' ?>">الألعاب</a>
        <a href="contact.php" class="<?= $current == 'contact.php' ? 'active' : '' ?>">تواصل</a>
        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role']=='admin'): ?>
            <a href="dashboard_admin.php" class="<?= $current == 'dashboard_admin.php' ? 'active' : '' ?>">لوحة التحكم</a> 
        <?php endif; ?>
         <a href="profile.php" class="<?= $current == 'dashboard_admin.php' ? 'active' : '' ?>">الحساب</a>
           
        <i>المهندسة ريم طاهر الوعيل</i>
    </div>
    <div class="header-search">
        <form action="search.php" method="get" style="margin:0;">
            <input type="text" name="q" placeholder="ابحث عن الكتب...">
        </form>
    </div>
    <a href="orders.php" class="<?= $current == 'dashboard_admin.php' ? 'active' : '' ?>">طلباتي</a>
         
    <div class="header-user">
        <?php if(isset($_SESSION['user_id'])): ?>
            <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? 'assets/img/default-user.png') ?>" class="header-user-img" alt="صورة">
            <span><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
            <button onclick="window.location.href='logout.php'" class="header-btn" style="background:#e75e5e;">خروج</button>
        <?php else: ?>
            <a href="login_register.php" class="header-btn"><i class="fa fa-sign-in-alt"></i> دخول / تسجيل</a>
        <?php endif; ?>
    </div>
</div>
