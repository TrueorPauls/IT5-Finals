<?php

if (!isset($conn)) {
    include("config.php");
}

$_theme = 'light';
$_theme_res = mysqli_query($conn, "SELECT setting_value FROM sandbox_settings WHERE setting_key = 'theme' LIMIT 1");
if ($_theme_res && mysqli_num_rows($_theme_res) > 0) {
    $_theme_row = mysqli_fetch_assoc($_theme_res);
    $_theme = $_theme_row['setting_value'];
}

if ($_theme === 'dark'):
?>
<style>
/* =================
   DARK MODE
   ================= */

/* ---- Shared: body, nav already dark ---- */
body {
    background-color: #1a1210 !important;
    color: #f0ebe3 !important;
}

/* ---- index.php & admin_dashboard.php container ---- */
.container {
    background-color: #1a1210 !important;
}

/* ---- Section / article / main content wrappers ---- */
section, article, main, .menu-page, .coffee-page {
    background-color: #1a1210 !important;
}

/* ---- Sidebar (coffee, food, events, sandbox) ---- */
.menu-sidebar {
    background-color: #0d3320 !important;
}

/* ---- Menu items area ---- */
.menu-items {
    background-color: #1a1210 !important;
}

/* ---- Section headings ---- */
.section-title, .sticky-header, h3.section-title {
    color: #4ade80 !important;
    border-bottom-color: #4ade80 !important;
    background-color: #1a1210 !important;
}

/* ---- Item names, general text ---- */
.item-name, #foodtitle, h4, h5 {
    color: #f0ebe3 !important;
}

.item-detail, #desc, .page-header p {
    color: #9a8880 !important;
}

/* ---- Prices ---- */
.price, #foodprice, .unit-price {
    color: #4ade80 !important;
}

/* ---- Menu row cards ---- */
.menu-row, .food-item, .food-feature {
    background-color: #2a1f1c !important;
    color: #f0ebe3 !important;
}

.food-feature .feature-info {
    background-color: #2a1f1c !important;
}

/* ---- Cart page ---- */
.page-header h1 {
    color: #4ade80 !important;
}

.cart-wrapper, .cart-items-section {
    background-color: #1a1210 !important;
}

.cart-item {
    background: #2a1f1c !important;
    border-color: #3a2e2a !important;
    color: #f0ebe3 !important;
}

.cart-item-info h3 {
    color: #f0ebe3 !important;
}

.cart-item-info .category {
    color: #9a8880 !important;
}

.cart-summary-section .summary-box {
    background: #2a1f1c !important;
    color: #f0ebe3 !important;
}

.summary-line {
    border-bottom-color: #3a2e2a !important;
    color: #f0ebe3 !important;
}

.summary-line.total {
    color: #4ade80 !important;
}

.empty-cart {
    background: #2a1f1c !important;
    color: #f0ebe3 !important;
}

.empty-cart h2 {
    color: #4ade80 !important;
}

.empty-cart p {
    color: #9a8880 !important;
}

/* ---- Checkout page ---- */
.checkout-wrapper {
    background-color: #1a1210 !important;
}

.form-card, .review-box {
    background: #2a1f1c !important;
    color: #f0ebe3 !important;
}

.form-card h2, .review-box h2 {
    color: #4ade80 !important;
}

.form-group label {
    color: #f0ebe3 !important;
}

.form-group input,
.form-group textarea {
    background: #3a2e2a !important;
    border-color: #5a4a44 !important;
    color: #f0ebe3 !important;
}

.review-item {
    border-bottom-color: #3a2e2a !important;
    color: #f0ebe3 !important;
}

.review-item-name {
    color: #f0ebe3 !important;
}

.review-item-qty {
    color: #9a8880 !important;
}

.review-item-price {
    color: #4ade80 !important;
}

.review-total {
    color: #4ade80 !important;
    border-top-color: #3a2e2a !important;
}

.success-card {
    background: #2a1f1c !important;
    color: #f0ebe3 !important;
}

/* ---- Order History page ---- */
.history-wrapper {
    background-color: #1a1210 !important;
}

.order-card {
    background: #2a1f1c !important;
    color: #f0ebe3 !important;
    border-color: #3a2e2a !important;
}

.order-card-header {
    background: #14532d !important;
    color: #f0ebe3 !important;
}

.order-num {
    color: #f0ebe3 !important;
}

.order-date {
    color: #9a8880 !important;
}

.order-card-body {
    background-color: #2a1f1c !important;
}

.order-meta-item label {
    color: #9a8880 !important;
}

.order-meta-item span {
    color: #f0ebe3 !important;
}

.items-table {
    background: #1a1210 !important;
    color: #f0ebe3 !important;
}

.items-table thead tr {
    background: #14532d !important;
    color: #fff !important;
}

.items-table tbody tr {
    border-bottom-color: #3a2e2a !important;
    color: #f0ebe3 !important;
}

.items-table tbody tr:hover {
    background: #3a2e2a !important;
}

.order-notes {
    background: #3a2e2a !important;
    color: #9a8880 !important;
}

.order-total-row {
    background: #14532d !important;
    color: #f0ebe3 !important;
}

.filter-bar .filter-btn {
    background: #2a1f1c !important;
    color: #f0ebe3 !important;
    border-color: #3a2e2a !important;
}

.filter-bar .filter-btn.active {
    background: #14532d !important;
    color: #fff !important;
}

.empty-orders {
    background: #2a1f1c !important;
    color: #f0ebe3 !important;
}

.empty-orders h2 {
    color: #4ade80 !important;
}

.empty-orders p {
    color: #9a8880 !important;
}

/* ---- Events page ---- */
.food-feature .feature-info h4 {
    color: #4ade80 !important;
}

/* ---- Report page ---- */
#kablogo {
    background-color: #1a1210 !important;
    color: #f0ebe3 !important;
}
body {
    background-color: #1a1210 !important;
    color: #f0ebe3 !important;
}
#intro {
    color: #9a8880 !important;
}
.loginbox {
    background-color: #2a1f1c !important;
}
#n, #em, #issuecateg, #desc {
    color: #f0ebe3 !important;
    background-color: #3a2e2a !important;
    border-color: #5a4a44 !important;
}

/* ---- Admin pages: orders.php, reportrepository.php ---- */
.ordertaker, .table-container {
    background-color: #1a1210 !important;
}

.order-table {
    background: #1a1210 !important;
    color: #f0ebe3 !important;
}

.order-table thead tr {
    background-color: #14532d !important;
    color: #fff !important;
}

.order-table tbody tr {
    background-color: #2a1f1c !important;
    color: #f0ebe3 !important;
    border-bottom-color: #3a2e2a !important;
}

.order-table tbody tr:hover {
    background-color: #3a2e2a !important;
}

#ordertitle, #reporttitle {
    color: #4ade80 !important;
}

.status-dropdown {
    background: #3a2e2a !important;
    color: #f0ebe3 !important;
    border-color: #5a4a44 !important;
}

/* ---- Index / Admin dashboard hero ---- */
#tagline {
    color: #4ade80 !important;
}

#tagdesc {
    color: #f0ebe3 !important;
}

#aboutus {
    color: #f0ebe3 !important;
}

#story p {
    color: #c8b8b0 !important;
}

/* Fix images for dark mode on index and events pages */
#taglineimg,
#storyimg,
#storyimg2,
#storyimg3,
.food-feature img,
.logo img,
.logoimg {
    filter: brightness(0.85) contrast(1.1) drop-shadow(0 2px 8px rgba(0,0,0,0.5));
    border: 2px solid rgba(255,255,255,0.08);
    background: #2a1f1c !important;
}

/* Make sure images with white backgrounds are not too bright in dark mode */
img[src$=".webp"],
img[src$=".jpg"],
img[src$=".jpeg"],
img[src$=".png"] {
    background: #2a1f1c !important;
    border-radius: 8px;
}

/* ---- Login & Signup pages ---- */
.loginbox, .login {
    background: #2a1f1c !important;
    color: #f0ebe3 !important;
}

.login h1 {
    color: #4ade80 !important;
}

#intro {
    color: #9a8880 !important;
}

#un, #pw, .fn, .un, p#un, p#pw {
    color: #f0ebe3 !important;
}

.login input[type="text"],
.login input[type="password"] {
    background: #3a2e2a !important;
    border-color: #5a4a44 !important;
    color: #f0ebe3 !important;
}

#or, #sign, #return {
    color: #9a8880 !important;
}

/* ---- Sandbox settings page ---- */
.sandbox-page .menu-items {
    background-color: #1a1210 !important;
}

.settings-card {
    background-color: #2a1f1c !important;
    color: #f0ebe3 !important;
}

.settings-card h3 {
    color: #4ade80 !important;
    border-bottom-color: #3a2e2a !important;
}

.section-title {
    color: #4ade80 !important;
    border-bottom-color: #4ade80 !important;
}

.form-row {
    border-bottom-color: #3a2e2a !important;
}

.form-row label {
    color: #f0ebe3 !important;
}

.form-row input[type="text"],
.form-row input[type="number"] {
    background-color: #3a2e2a !important;
    border-color: #5a4a44 !important;
    color: #f0ebe3 !important;
}

/* ---- highlight cards on index ---- */
.card {
    background-color: rgba(42, 31, 28, 0.85) !important;
    color: #f0ebe3 !important;
}

.card h3 {
    color: #4ade80 !important;
}

.card p {
    color: #c8b8b0 !important;
}

/* ---- footer already dark in most pages, keep it ---- */
footer {
    background-color: #0a0806 !important;
    color: #d8e2dc !important;
}
</style>
<?php endif; ?>