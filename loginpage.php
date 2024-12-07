<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Who Are You? - Advanced Library Management System</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <style>
        .role-selection {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .role-box {
            width: 200px;
            height: 100px;
            border: 2px solid #333;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none; /* Ensure no underline on hover */
            color: inherit; /* Ensure text color doesn't change */
        }

        .role-box:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Who Are You?</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("startnavbar.php"); ?>
        <?php require("./styles/darkmodeandreader.php"); ?>

        <!-- Role Selection Section -->
        <div class="about-section">
            <h2>Select Your Role</h2>
            <div class="role-selection">
                <!-- Accountant -->
                <a href="./accountant/login_accountant.php" class="role-box">Accountant</a>
                
                <!-- Call Center Operator -->
                <a href="./callcenter/login_callcenter.php" class="role-box">Call Center Operator</a>
                
                <!-- Admin -->
                <a href="./admin/login_admin.php" class="role-box">Admin</a>
                
                <!-- Branch Manager -->
                <a href="./branchmanager/login_branchmanager.php" class="role-box">Branch Manager</a>

                <!-- Librarian -->
                <a href="./librarian/login_librarian.php" class="role-box">Librarian</a>
                
                <!-- Library Member -->
                <a href="./member/login_member.php" class="role-box">Library Member</a>

                <!-- Purchase Manager -->
                <a href="./purchasemanager/login_purchasemanager.php" class="role-box">Purchase Manager</a>
            </div>
        </div>

        <!-- Footer -->
        <?php require("footer.php"); ?>
    </div>
</body>
</html>
