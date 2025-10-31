<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>База данных магазинов</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #ddd; }
    </style>
</head>
<body>
    <h1>База данных магазинов - SQLite</h1>
<?php
try {
    $db = new PDO('sqlite:shop.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA foreign_keys = ON');
    
    $checkTable = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='shop'");
    if (!$checkTable->fetch()) {
        $sql = file_get_contents('database.sql');
        $sql = preg_replace('/--[^\n]*\n/', '', $sql);
        $sql = preg_replace('/PRAGMA[^;]*;/', '', $sql);
        foreach (explode(';', $sql) as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try { $db->exec($statement); } catch (PDOException $e) {}
            }
        }
        echo '<p>База данных инициализирована</p>';
    }
    
    echo '<h2>Магазины</h2><table><tr><th>ID</th><th>Название</th><th>Адрес</th></tr>';
    $result = $db->query('SELECT * FROM shop');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td>'.$row['id'].'</td><td>'.$row['name'].'</td><td>'.$row['address'].'</td></tr>';
    }
    echo '</table>';
    
    echo '<h2>Клиенты</h2><table><tr><th>ID</th><th>Имя</th><th>Телефон</th></tr>';
    $result = $db->query('SELECT * FROM client');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td>'.$row['id'].'</td><td>'.$row['name'].'</td><td>'.$row['phone'].'</td></tr>';
    }
    echo '</table>';
    
    echo '<h2>Продукты</h2><table><tr><th>ID</th><th>Название</th><th>Цена</th><th>Количество</th><th>Магазин</th></tr>';
    $result = $db->query('SELECT p.*, s.name as shop_name FROM product p JOIN shop s ON p.shop_id = s.id ORDER BY s.name, p.name');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td>'.$row['id'].'</td><td>'.$row['name'].'</td><td>'.$row['price'].'</td><td>'.$row['count'].'</td><td>'.$row['shop_name'].'</td></tr>';
    }
    echo '</table>';
    
    echo '<h2>Заказы</h2><table><tr><th>ID</th><th>Дата и время</th><th>Клиент</th><th>Магазин</th></tr>';
    $result = $db->query('SELECT o.*, c.name as client_name, s.name as shop_name FROM "order" o JOIN client c ON o.client_id = c.id JOIN shop s ON o.shop_id = s.id ORDER BY o.created_at DESC');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td>'.$row['id'].'</td><td>'.$row['created_at'].'</td><td>'.$row['client_name'].'</td><td>'.$row['shop_name'].'</td></tr>';
    }
    echo '</table>';
    
    echo '<h2>Детали заказов</h2><table><tr><th>ID</th><th>№ заказа</th><th>Клиент</th><th>Продукт</th><th>Количество</th><th>Цена</th><th>Сумма</th></tr>';
    $result = $db->query('SELECT op.*, o.id as order_id, c.name as client_name, p.name as product_name, (op.quantity * op.price) as total FROM order_product op JOIN "order" o ON op.order_id = o.id JOIN client c ON o.client_id = c.id JOIN product p ON op.product_id = p.id ORDER BY o.id');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td>'.$row['id'].'</td><td>'.$row['order_id'].'</td><td>'.$row['client_name'].'</td><td>'.$row['product_name'].'</td><td>'.$row['quantity'].'</td><td>'.$row['price'].'</td><td>'.$row['total'].'</td></tr>';
    }
    echo '</table>';
    
} catch (PDOException $e) {
    echo '<p style="color:red;">Ошибка: ' . $e->getMessage() . '</p>';
}
?>

</body>
</html>

