<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Медицинский центр</title>
</head>
<body>
    <h1>Медицинский центр - Управление данными</h1>
    
    <h2>Поиск пациентов</h2>
    <form method="GET" action="search.php">
        <input type="text" name="search_name" placeholder="Имя пациента">
        <input type="text" name="search_surname" placeholder="Фамилия пациента">
        <button type="submit">Найти</button>
    </form>
    
    <h2>Добавить пациента</h2>
    <form method="POST" action="add_patient.php">
        <input type="text" name="name" placeholder="Имя" required>
        <input type="text" name="surname" placeholder="Фамилия" required>
        <input type="text" name="patronymic" placeholder="Отчество">
        <select name="gender" required>
            <option value="мужской">Мужской</option>
            <option value="женский">Женский</option>
        </select>
        <input type="date" name="dateofbirth" required>
        <input type="tel" name="telephone" placeholder="Телефон">
        <button type="submit">Добавить пациента</button>
    </form>
    
    <h2>Список пациентов</h2>
    <?php
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.surname, p.patronymic, p.gender, p.dateofbirth, p.telephone 
        FROM profile p 
        WHERE p.role = 'Юзер'
        LIMIT 10
    ");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($patients): ?>
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
        <p>Нет данных о пациентах</p>
    <?php endif; ?>
    
    <h2>Врачи и отделения</h2>
    <?php
    $stmt = $pdo->query("
        SELECT d.id, p.name, p.surname, p.patronymic, d.specialization, dep.name as department
        FROM doctors d
        JOIN profile p ON d.profile_id = p.id
        LEFT JOIN departments dep ON d.department_id = dep.id
        WHERE d.is_active = TRUE
    ");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($doctors): ?>
        <table border="1">
            <tr>
                <th>ФИО врача</th>
                <th>Специализация</th>
                <th>Отделение</th>
            </tr>
            <?php foreach ($doctors as $doctor): ?>
            <tr>
                <td><?= $doctor['surname'] ?> <?= $doctor['name'] ?> <?= $doctor['patronymic'] ?></td>
                <td><?= $doctor['specialization'] ?></td>
                <td><?= $doctor['department'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Нет данных о врачах</p>
    <?php endif; ?>
</body>
</html>