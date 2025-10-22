<?php
require_once 'config.php';

$search_name = $_GET['search_name'] ?? '';
$search_surname = $_GET['search_surname'] ?? '';

$sql = "SELECT p.id, p.name, p.surname, p.patronymic, p.gender, p.dateofbirth, p.telephone 
        FROM profile p 
        WHERE p.role = 'Юзер'";
$params = [];

if (!empty($search_name)) {
    $sql .= " AND p.name LIKE ?";
    $params[] = "%$search_name%";
}

if (!empty($search_surname)) {
    $sql .= " AND p.surname LIKE ?";
    $params[] = "%$search_surname%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Результаты поиска</title>
</head>
<body>
    <h1>Результаты поиска</h1>
    <a href="index.php">Назад</a>
    
    <?php if ($patients): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Пол</th>
                <th>Дата рождения</th>
                <th>Телефон</th>
            </tr>
            <?php foreach ($patients as $patient): ?>
            <tr>
                <td><?= $patient['id'] ?></td>
                <td><?= $patient['surname'] ?> <?= $patient['name'] ?> <?= $patient['patronymic'] ?></td>
                <td><?= $patient['gender'] ?></td>
                <td><?= $patient['dateofbirth'] ?></td>
                <td><?= $patient['telephone'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Пациенты не найдены</p>
    <?php endif; ?>
</body>
</html>