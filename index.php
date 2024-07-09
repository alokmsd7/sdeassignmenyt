<?php
require 'db.php';


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;


$price_min = isset($_GET['price_min']) && $_GET['price_min'] !== '' ? (float)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) && $_GET['price_max'] !== '' ? (float)$_GET['price_max'] : 1000000;
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : '';
$on_sale = isset($_GET['on_sale']) && $_GET['on_sale'] !== '-1' ? (int)$_GET['on_sale'] : -1;


$sql = "SELECT * FROM products WHERE 1=1";

if ($price_min != 0 || $price_max != 1000000) {
    $sql .= " AND price BETWEEN :price_min AND :price_max";
}
if ($category) {
    $sql .= " AND category = :category";
}
if ($on_sale != -1) {
    $sql .= " AND on_sale = :on_sale";
}
$sql .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

if ($price_min != 0 || $price_max != 1000000) {
    $stmt->bindParam(':price_min', $price_min, PDO::PARAM_STR);
    $stmt->bindParam(':price_max', $price_max, PDO::PARAM_STR);
}
if ($category) {
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
}
if ($on_sale != -1) {
    $stmt->bindParam(':on_sale', $on_sale, PDO::PARAM_INT);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_sql = "SELECT COUNT(*) FROM products WHERE 1=1";
if ($price_min != 0 || $price_max != 1000000) {
    $total_sql .= " AND price BETWEEN :price_min AND :price_max";
}
if ($category) {
    $total_sql .= " AND category = :category";
}
if ($on_sale != -1) {
    $total_sql .= " AND on_sale = :on_sale";
}

$total_stmt = $pdo->prepare($total_sql);

if ($price_min != 0 || $price_max != 1000000) {
    $total_stmt->bindParam(':price_min', $price_min, PDO::PARAM_STR);
    $total_stmt->bindParam(':price_max', $price_max, PDO::PARAM_STR);
}
if ($category) {
    $total_stmt->bindParam(':category', $category, PDO::PARAM_STR);
}
if ($on_sale != -1) {
    $total_stmt->bindParam(':on_sale', $on_sale, PDO::PARAM_INT);
}
$total_stmt->execute();
$total_products = $total_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listing</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <div class="filters">
        <form method="GET" action="index.php">
            <label for="price_min">Price Min:</label>
            <input type="number" name="price_min" id="price_min" value="<?= htmlspecialchars($price_min) ?>">

            <label for="price_max">Price Max:</label>
            <input type="number" name="price_max" id="price_max" value="<?= htmlspecialchars($price_max) ?>">

            <label for="category">Category:</label>
            <select name="category" id="category">
                <option value="">All</option>
                <option value="Category 1" <?= $category == "Category 1" ? 'selected' : '' ?>>Category 1</option>
                <option value="Category 2" <?= $category == "Category 2" ? 'selected' : '' ?>>Category 2</option>
            </select>

            <label for="on_sale">On Sale:</label>
            <select name="on_sale" id="on_sale">
                <option value="-1">All</option>
                <option value="1" <?= $on_sale == 1 ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= $on_sale == 0 ? 'selected' : '' ?>>No</option>
            </select>

            <button type="submit">Apply Filters</button>
        </form>
    </div>

    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p>Price: $<?= htmlspecialchars($product['price']) ?></p>
                <p>Category: <?= htmlspecialchars($product['category']) ?></p>
                <p>On Sale: <?= $product['on_sale'] ? 'Yes' : 'No' ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&price_min=<?= htmlspecialchars($price_min) ?>&price_max=<?= htmlspecialchars($price_max) ?>&category=<?= htmlspecialchars($category) ?>&on_sale=<?= htmlspecialchars($on_sale) ?>" 
               class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>

    <?php include 'templates/footer.php'; ?>
    <script src="js/scripts.js"></script>
</body>
</html>
