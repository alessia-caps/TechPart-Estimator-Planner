<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Builds</title>
    <link rel="stylesheet" href="pcbuildstyles.css">
    <script defer src="script.js"></script>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Web Name">
        </div>
        <nav>
            <a href="../index.php">Products</a>
            <a href="#" class="active">PC Builds</a>
            <div class="auth-section">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Display the logged-in user's first and last name -->
                    <span>Welcome, <a href="../profile.php"><?php echo htmlspecialchars($_SESSION['first_name'] . " " . $_SESSION['last_name']); ?></a>!</span>
                    <a href="../logout.php">Sign Out</a>
                <?php else: ?>
                    <!-- If not logged in, show Sign In link -->
                    <a href="../login.html">Sign In</a>
                    <div class="account">
                        <img src="profile-placeholder.png" alt="User" class="account-icon">
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </div>

    <div class="container">
        <div class="sidebar">
            <button class="save-button" onclick="saveDraft()">Save as</button>
            <button class="new-button" onclick="clearBuild()">New</button>
            <div class="results">
                <p>Estimated Wattage: <span id="total-wattage">0</span>W</p>
                <p>Compatibility: <span id="compatibility-status">No</span></p>
            </div>
        </div>
        
        <div class="build-area">
            <table>
                <tr>
                    <th>Component</th>
                    <th>Select</th>
                    <th>Product</th>
                    <th>Wattage</th>
                    <th>From</th>
                    <th>Remove</th>
                </tr>
                <tr id="cpu-row">
                    <td>CPU</td>
                    <td>
                        <select id="cpu-dropdown" class="component-dropdown" onclick="searchComponent('cpu')" onchange="selectComponent('cpu', event)">
                            <option value="">Select CPU</option>
                        </select>
                    </td>
                    <td id="cpu-product">-</td>
                    <td id="cpu-wattage">-</td>
                    <td id="cpu-store">-</td>
                    <td><button class="remove" onclick="removeComponent('cpu')">×</button></td>
                </tr>
                <tr id="motherboard-row">
                    <td>Motherboard</td>
                    <td>
                        <select id="motherboard-dropdown" class="component-dropdown" onclick="searchComponent('motherboard')" onchange="selectComponent('motherboard', event)">
                            <option value="">Select Motherboard</option>
                        </select>
                    </td>
                    <td id="motherboard-product">-</td>
                    <td id="motherboard-wattage">-</td>
                    <td id="motherboard-store">-</td>
                    <td><button class="remove" onclick="removeComponent('motherboard')">×</button></td>
                </tr>
                <tr id="ram-row">
                    <td>RAM</td>
                    <td>
                        <select id="ram-dropdown" class="component-dropdown" onclick="searchComponent('ram')" onchange="selectComponent('ram', event)">
                            <option value="">Select RAM</option>
                        </select>
                    </td>
                    <td id="ram-product">-</td>
                    <td id="ram-wattage">-</td>
                    <td id="ram-store">-</td>
                    <td><button class="remove" onclick="removeComponent('ram')">×</button></td>
                </tr>
                <tr id="gpu-row">
                    <td>GPU</td>
                    <td>
                        <select id="gpu-dropdown" class="component-dropdown" onclick="searchComponent('gpu')" onchange="selectComponent('gpu', event)">
                            <option value="">Select GPU</option>
                        </select>
                    </td>
                    <td id="gpu-product">-</td>
                    <td id="gpu-wattage">-</td>
                    <td id="gpu-store">-</td>
                    <td><button class="remove" onclick="removeComponent('gpu')">×</button></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
