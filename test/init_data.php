<?php
require_once 'config.php';

try {
    // Добавляем отделения
    $departments = [
        ['Терапевтическое отделение', 1, '101', NULL],
        ['Хирургическое отделение', 2, '201', NULL],
        ['Неврологическое отделение', 1, '102', NULL]
    ];
    
    foreach ($departments as $dept) {
        $stmt = $pdo->prepare("INSERT INTO departments (name, floor, phone_extension, head_doctor_id) VALUES (?, ?, ?, ?)");
        $stmt->execute($dept);
    }
    
    // Добавляем врачей
    $doctors = [
        ['Иван', 'Петров', 'Сергеевич', 'мужской', 'терапевт', 1, '101', 'Пн-Пт 9:00-18:00'],
        ['Мария', 'Иванова', 'Владимировна', 'женский', 'хирург', 2, '201', 'Пн-Пт 8:00-17:00'],
        ['Алексей', 'Сидоров', 'Анатольевич', 'мужской', 'невролог', 3, '102', 'Пн-Пт 10:00-19:00']
    ];
    
    foreach ($doctors as $doctor) {
        // Создаем медицинскую карту для врача
        $card_number = 'DOC' . date('YmdHis') . rand(100, 999);
        $stmt = $pdo->prepare("INSERT INTO medical_card (card_number, issue_date) VALUES (?, CURDATE())");
        $stmt->execute([$card_number]);
        $medical_card_id = $pdo->lastInsertId();
        
        // Создаем документ
        $stmt = $pdo->prepare("INSERT INTO document (medical_card_id) VALUES (?)");
        $stmt->execute([$medical_card_id]);
        $document_id = $pdo->lastInsertId();
        
        // Создаем профиль врача
        $stmt = $pdo->prepare("
            INSERT INTO profile (name, surname, patronymic, gender, document_id, role) 
            VALUES (?, ?, ?, ?, ?, 'Доктор')
        ");
        $stmt->execute([$doctor[0], $doctor[1], $doctor[2], $doctor[3], $document_id]);
        $profile_id = $pdo->lastInsertId();
        
        // Создаем запись врача
        $stmt = $pdo->prepare("
            INSERT INTO doctors (profile_id, specialization, department_id, room_number, work_schedule) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$profile_id, $doctor[4], $doctor[5], $doctor[6], $doctor[7]]);
    }
    
    echo "Тестовые данные успешно добавлены!";
    
} catch (Exception $e) {
    die("Ошибка при добавлении тестовых данных: " . $e->getMessage());
}
?>