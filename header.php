<?php

echo ("<style>
    .header {
        position: sticky;
        top: 0;
        padding: 10px 20px;
        background: #2c3e50;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }
    .header .logo {
        font-size: 1.8em;
        font-weight: bold;
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    .header .logo img {
        width: 40px;
        height: 40px;
        margin-right: 10px;
    }
    .header .nav-links {
        display: flex;
        gap: 20px;
        align-items: center
    }
    .header .nav-links a {
        text-decoration: none;
        color: white;
        font-size: 1.1em;
        font-weight: 500;
        transition: color 0.3s ease;
        display: flex;              
        align-items: center;          
        justify-content: center;      
        height: 100%;    
    }
    .header .nav-links a:hover {
        color: #ecf0f1;
    }
    .header .nav-links .logout {
        background-color: #e74c3c;
        padding: 8px 16px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    .header .nav-links .logout:hover {
        background-color: #c0392b;
    }
</style>");

echo ('<div class="header" id="myHeader">
    <a href="home.php" class="logo">
        <img src="files/homeLogo.png" alt="Logo">
        Sudoku Variants
    </a>
    <div class="nav-links">
        <a href="userInfo.php"><div>User Info</div></a>
        <a href="gestori/logout.php" class="logout">Logout</a>
    </div>
</div>');
?>
