<?php
session_start();
include("config.php");

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: log-in.php");
    exit;
}

// ---- load all settings from DB ----
function load_settings($conn) {
    $settings = array();
    $result = mysqli_query($conn, "SELECT setting_key, setting_value FROM sandbox_settings");
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

// ---- upsert one setting ----
function save_setting($conn, $key, $value) {
    $key   = mysqli_real_escape_string($conn, $key);
    $value = mysqli_real_escape_string($conn, $value);
    mysqli_query($conn,
        "INSERT INTO sandbox_settings (setting_key, setting_value)
         VALUES ('$key', '$value')
         ON DUPLICATE KEY UPDATE setting_value = '$value'"
    );
}

$msg      = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Save Theme
    if (isset($_POST['save_theme'])) {
        $theme = (isset($_POST['theme']) && $_POST['theme'] === 'dark') ? 'dark' : 'light';
        save_setting($conn, 'theme', $theme);
        $msg      = "Theme saved successfully!";
        $msg_type = "success";
    }

    // Save Coffee Menu
    if (isset($_POST['save_coffee'])) {
        $coffee_ids = array('c1','c2','c3','c4','c5','c6','c7','c8','c9');
        foreach ($coffee_ids as $id) {
            $name  = isset($_POST[$id . '_name'])  ? trim($_POST[$id . '_name'])  : '';
            $price = isset($_POST[$id . '_price']) ? trim($_POST[$id . '_price']) : '0';
            if ($name !== '') {
                save_setting($conn, $id . '_name',  $name);
                save_setting($conn, $id . '_price', $price);
            }
        }
        $msg      = "Coffee menu saved successfully!";
        $msg_type = "success";
    }

    // Save Food Menu
    if (isset($_POST['save_food'])) {
        $food_ids = array('f10','f11','f12','f13','f14','f15','f16');
        foreach ($food_ids as $id) {
            $name  = isset($_POST[$id . '_name'])  ? trim($_POST[$id . '_name'])  : '';
            $price = isset($_POST[$id . '_price']) ? trim($_POST[$id . '_price']) : '0';
            if ($name !== '') {
                save_setting($conn, $id . '_name',  $name);
                save_setting($conn, $id . '_price', $price);
            }
        }
        $msg      = "Food menu saved successfully!";
        $msg_type = "success";
    }

    // Reset Coffee
    if (isset($_POST['reset_coffee'])) {
        $defaults = array(
            'c1' => array('Espresso', '130'),
            'c2' => array('Americano', '130'),
            'c3' => array('Cappuccino', '155'),
            'c4' => array('Pandan Latte', '185'),
            'c5' => array('Spanish Latte', '175'),
            'c6' => array('Salted Caramel Latte', '180'),
            'c7' => array('Premium Hot Cocoa', '160'),
            'c8' => array('Matcha Latte', '185'),
            'c9' => array('Chocolate Milk', '150'),
        );
        foreach ($defaults as $id => $val) {
            save_setting($conn, $id . '_name',  $val[0]);
            save_setting($conn, $id . '_price', $val[1]);
        }
        $msg      = "Coffee menu reset to default.";
        $msg_type = "success";
    }

    // Reset Food
    if (isset($_POST['reset_food'])) {
        $defaults = array(
            'f10' => array('The Sharing Spread', '450'),
            'f11' => array('House Blends', '165'),
            'f12' => array('Classic Tapa Silog', '320'),
            'f13' => array('Truffle Adobo Pasta', '380'),
            'f14' => array('Lechon Kawali', '425'),
            'f15' => array('Artisanal Fruit Donuts', '95'),
            'f16' => array('Botanical Cakes', 'Custom'),
        );
        foreach ($defaults as $id => $val) {
            save_setting($conn, $id . '_name',  $val[0]);
            save_setting($conn, $id . '_price', $val[1]);
        }
        $msg      = "Food menu reset to default.";
        $msg_type = "success";
    }

    // Reset Theme
    if (isset($_POST['reset_theme'])) {
        save_setting($conn, 'theme', 'light');
        $msg      = "Theme reset to Light Mode.";
        $msg_type = "success";
    }
}

// ---- Load current settings ----
$s = load_settings($conn);

$theme = (isset($s['theme']) && $s['theme'] === 'dark') ? 'dark' : 'light';

// FIX: define body/sidebar colors based on saved theme
$body_bg      = ($theme === 'dark') ? '#1a1210' : '#d8e2dc';
$body_color   = ($theme === 'dark') ? '#f0ebe3' : '#3c2f2c';
$card_bg      = ($theme === 'dark') ? '#2a1f1c' : '#ffffff';
$card_color   = ($theme === 'dark') ? '#f0ebe3' : '#3c2f2c';
$input_bg     = ($theme === 'dark') ? '#3a2e2a' : '#fafafa';
$input_border = ($theme === 'dark') ? '#5a4a44' : '#cccccc';
$input_color  = ($theme === 'dark') ? '#f0ebe3' : '#3c2f2c';
$sidebar_bg   = ($theme === 'dark') ? '#0d3320' : '#14532d';

$coffee = array(
    'c1' => array('name' => isset($s['c1_name']) ? $s['c1_name'] : 'Espresso',            'price' => isset($s['c1_price']) ? $s['c1_price'] : '130'),
    'c2' => array('name' => isset($s['c2_name']) ? $s['c2_name'] : 'Americano',           'price' => isset($s['c2_price']) ? $s['c2_price'] : '130'),
    'c3' => array('name' => isset($s['c3_name']) ? $s['c3_name'] : 'Cappuccino',          'price' => isset($s['c3_price']) ? $s['c3_price'] : '155'),
    'c4' => array('name' => isset($s['c4_name']) ? $s['c4_name'] : 'Pandan Latte',        'price' => isset($s['c4_price']) ? $s['c4_price'] : '185'),
    'c5' => array('name' => isset($s['c5_name']) ? $s['c5_name'] : 'Spanish Latte',       'price' => isset($s['c5_price']) ? $s['c5_price'] : '175'),
    'c6' => array('name' => isset($s['c6_name']) ? $s['c6_name'] : 'Salted Caramel Latte','price' => isset($s['c6_price']) ? $s['c6_price'] : '180'),
    'c7' => array('name' => isset($s['c7_name']) ? $s['c7_name'] : 'Premium Hot Cocoa',   'price' => isset($s['c7_price']) ? $s['c7_price'] : '160'),
    'c8' => array('name' => isset($s['c8_name']) ? $s['c8_name'] : 'Matcha Latte',        'price' => isset($s['c8_price']) ? $s['c8_price'] : '185'),
    'c9' => array('name' => isset($s['c9_name']) ? $s['c9_name'] : 'Chocolate Milk',      'price' => isset($s['c9_price']) ? $s['c9_price'] : '150'),
);

$food = array(
    'f10' => array('name' => isset($s['f10_name']) ? $s['f10_name'] : 'The Sharing Spread',     'price' => isset($s['f10_price']) ? $s['f10_price'] : '450'),
    'f11' => array('name' => isset($s['f11_name']) ? $s['f11_name'] : 'House Blends',           'price' => isset($s['f11_price']) ? $s['f11_price'] : '165'),
    'f12' => array('name' => isset($s['f12_name']) ? $s['f12_name'] : 'Classic Tapa Silog',     'price' => isset($s['f12_price']) ? $s['f12_price'] : '320'),
    'f13' => array('name' => isset($s['f13_name']) ? $s['f13_name'] : 'Truffle Adobo Pasta',    'price' => isset($s['f13_price']) ? $s['f13_price'] : '380'),
    'f14' => array('name' => isset($s['f14_name']) ? $s['f14_name'] : 'Lechon Kawali',          'price' => isset($s['f14_price']) ? $s['f14_price'] : '425'),
    'f15' => array('name' => isset($s['f15_name']) ? $s['f15_name'] : 'Artisanal Fruit Donuts', 'price' => isset($s['f15_price']) ? $s['f15_price'] : '95'),
    'f16' => array('name' => isset($s['f16_name']) ? $s['f16_name'] : 'Botanical Cakes',        'price' => isset($s['f16_price']) ? $s['f16_price'] : 'Custom'),
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandbox Settings - Kabesera Cafe</title>
    <link rel="stylesheet" href="sandbox style.css">
    <!-- FIX: Apply theme colors inline so they always reflect the saved DB value -->
    <style>
        body {
            background-color: <?php echo $body_bg; ?>;
            color: <?php echo $body_color; ?>;
        }
        .menu-sidebar {
            background-color: <?php echo $sidebar_bg; ?>;
        }
        .settings-card {
            background-color: <?php echo $card_bg; ?>;
            color: <?php echo $card_color; ?>;
        }
        .settings-card h3 {
            color: <?php echo ($theme === 'dark') ? '#4ade80' : '#14532d'; ?>;
            border-bottom-color: <?php echo ($theme === 'dark') ? '#3a4a3a' : 'rgba(20,83,45,0.15)'; ?>;
        }
        .section-title {
            color: <?php echo ($theme === 'dark') ? '#4ade80' : '#14532d'; ?>;
            border-bottom-color: <?php echo ($theme === 'dark') ? '#4ade80' : '#14532d'; ?>;
        }
        .form-row label {
            color: <?php echo $card_color; ?>;
        }
        .form-row {
            border-bottom-color: <?php echo ($theme === 'dark') ? '#3a2e2a' : '#f0f0f0'; ?>;
        }
        .form-row input[type="text"],
        .form-row input[type="number"] {
            background-color: <?php echo $input_bg; ?>;
            border-color: <?php echo $input_border; ?>;
            color: <?php echo $input_color; ?>;
        }
        .menu-items {
            background-color: <?php echo $body_bg; ?>;
        }
        footer {
            background-color: <?php echo ($theme === 'dark') ? '#0a0806' : '#3c2f2c'; ?>;
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">
        <a href="admin_dashboard.php"><img src="qt=q_95.webp" alt="Kabesera Cafe"></a>
        <a id="kabhome" href="admin_dashboard.php">Kabesera Cafe</a>
    </div>
    <ul>
        <li><a href="orders.php">Order Management</a></li>
        <li><a href="reportrepository.php">Insights</a></li>
        <li><a href="book_event.php">Event Reservations</a></li>
        <li><a href="sandbox.php">Sandbox Settings</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
    </ul>
</nav>

<div class="sandbox-page">

    <div class="menu-sidebar">
        <div class="sidebar-content">
            <h2>Settings</h2>
            <ul class="sidebar-nav">
                <li><a href="#theme">Color Theme</a></li>
                <li><a href="#coffee">Coffee Menu</a></li>
                <li><a href="#food">Food Menu</a></li>
            </ul>
        </div>
    </div>

    <div class="menu-items">

        <?php if ($msg !== ""): ?>
        <div class="msg-<?php echo $msg_type; ?>">
            <?php echo htmlspecialchars($msg); ?>
        </div>
        <?php endif; ?>

        <!-- COLOR THEME -->
        <section id="theme" class="menu-section">
            <h2 class="section-title">Color Theme</h2>
            <div class="settings-card">
                <h3>Choose a theme for the customer pages</h3>
                <p style="font-size:0.85rem; margin-bottom:16px; color:<?php echo ($theme==='dark')?'#9a8880':'#666'; ?>">
                    This changes the background and text color on the Coffee and Food pages that customers see.
                </p>
                <form method="POST" action="sandbox.php#theme">
                    <div class="theme-options">
                        <label class="theme-card <?php echo $theme === 'light' ? 'selected' : ''; ?>">
                            <input type="radio" name="theme" value="light" <?php echo $theme === 'light' ? 'checked' : ''; ?> style="display:none;">
                            <div class="theme-preview-box light-preview">
                                <div class="preview-nav-strip"></div>
                                <div class="preview-body">
                                    <div class="preview-sidebar-strip"></div>
                                    <div class="preview-content-strip"></div>
                                </div>
                            </div>
                            <p>&#9728; Light Mode</p>
                        </label>
                        <label class="theme-card <?php echo $theme === 'dark' ? 'selected' : ''; ?>">
                            <input type="radio" name="theme" value="dark" <?php echo $theme === 'dark' ? 'checked' : ''; ?> style="display:none;">
                            <div class="theme-preview-box dark-preview">
                                <div class="preview-nav-strip"></div>
                                <div class="preview-body">
                                    <div class="preview-sidebar-strip"></div>
                                    <div class="preview-content-strip"></div>
                                </div>
                            </div>
                            <p>&#9790; Dark Mode</p>
                        </label>
                    </div>
                    <br>
                    <button type="submit" name="save_theme" class="save-btn">Save Theme</button>
                    <button type="submit" name="reset_theme" class="reset-btn">Reset to Default</button>
                </form>
            </div>
        </section>

        <!-- COFFEE MENU -->
        <section id="coffee" class="menu-section">
            <h2 class="section-title">Coffee Menu</h2>
            <form method="POST" action="sandbox.php#coffee">

                <div class="settings-card">
                    <h3>Espresso Bar</h3>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c1_name" value="<?php echo htmlspecialchars($coffee['c1']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c1_price" value="<?php echo htmlspecialchars($coffee['c1']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c2_name" value="<?php echo htmlspecialchars($coffee['c2']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c2_price" value="<?php echo htmlspecialchars($coffee['c2']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c3_name" value="<?php echo htmlspecialchars($coffee['c3']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c3_price" value="<?php echo htmlspecialchars($coffee['c3']['price']); ?>" min="0">
                    </div>
                </div>

                <div class="settings-card">
                    <h3>Signature Lattes</h3>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c4_name" value="<?php echo htmlspecialchars($coffee['c4']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c4_price" value="<?php echo htmlspecialchars($coffee['c4']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c5_name" value="<?php echo htmlspecialchars($coffee['c5']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c5_price" value="<?php echo htmlspecialchars($coffee['c5']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c6_name" value="<?php echo htmlspecialchars($coffee['c6']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c6_price" value="<?php echo htmlspecialchars($coffee['c6']['price']); ?>" min="0">
                    </div>
                </div>

                <div class="settings-card">
                    <h3>Non-Coffee</h3>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c7_name" value="<?php echo htmlspecialchars($coffee['c7']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c7_price" value="<?php echo htmlspecialchars($coffee['c7']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c8_name" value="<?php echo htmlspecialchars($coffee['c8']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c8_price" value="<?php echo htmlspecialchars($coffee['c8']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="c9_name" value="<?php echo htmlspecialchars($coffee['c9']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="c9_price" value="<?php echo htmlspecialchars($coffee['c9']['price']); ?>" min="0">
                    </div>
                </div>

                <button type="submit" name="save_coffee" class="save-btn">Save Coffee Menu</button>
                <button type="submit" name="reset_coffee" class="reset-btn">Reset to Default</button>
            </form>
        </section>

        <!-- FOOD MENU -->
        <section id="food" class="menu-section">
            <h2 class="section-title">Food Menu</h2>
            <form method="POST" action="sandbox.php#food">

                <div class="settings-card">
                    <h3>Breakfast &amp; Snacks</h3>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="f10_name" value="<?php echo htmlspecialchars($food['f10']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="f10_price" value="<?php echo htmlspecialchars($food['f10']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="f11_name" value="<?php echo htmlspecialchars($food['f11']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="f11_price" value="<?php echo htmlspecialchars($food['f11']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="f12_name" value="<?php echo htmlspecialchars($food['f12']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="f12_price" value="<?php echo htmlspecialchars($food['f12']['price']); ?>" min="0">
                    </div>
                </div>

                <div class="settings-card">
                    <h3>Lunch &amp; Dinner</h3>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="f13_name" value="<?php echo htmlspecialchars($food['f13']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="f13_price" value="<?php echo htmlspecialchars($food['f13']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="f14_name" value="<?php echo htmlspecialchars($food['f14']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="f14_price" value="<?php echo htmlspecialchars($food['f14']['price']); ?>" min="0">
                    </div>
                </div>

                <div class="settings-card">
                    <h3>Bakery &amp; Sweets</h3>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="f15_name" value="<?php echo htmlspecialchars($food['f15']['name']); ?>">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="f15_price" value="<?php echo htmlspecialchars($food['f15']['price']); ?>" min="0">
                    </div>
                    <div class="form-row">
                        <label>Item Name</label>
                        <input type="text" name="f16_name" value="<?php echo htmlspecialchars($food['f16']['name']); ?>">
                        <label>Price note</label>
                        <input type="text" name="f16_price" value="<?php echo htmlspecialchars($food['f16']['price']); ?>">
                    </div>
                </div>

                <button type="submit" name="save_food" class="save-btn">Save Food Menu</button>
                <button type="submit" name="reset_food" class="reset-btn">Reset to Default</button>
            </form>
        </section>

    </div>
</div>

<footer>
    <p>Kabesera Cafe &copy; 2025 &mdash; Admin Sandbox Settings</p>
</footer>

</body>
</html>
