<?php
require_once 'config.php';

if ($_POST) {
    try {
        $pdo->beginTransaction();
        
        // Создаем медицинскую карту
        $card_number = 'MC' . date('YmdHis');
        $stmt = $pdo->prepare("INSERT INTO medical_card (card_number, issue_date) VALUES (?, CURDATE())");
        $stmt->execute([$card_number]);
        $medical_card_id = $pdo->lastInsertId();
        
        // Создаем документ
        $stmt = $pdo->prepare("INSERT INTO document (medical_card_id) VALUES (?)");
        $stmt->execute([$medical_card_id]);
        $document_id = $pdo->lastInsertId();
        
        // Создаем профиль
        $stmt = $pdo->prepare("
            INSERT INTO profile (name, surname, patronymic, gender, telephone, dateofbirth, document_id, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Юзер')
        ");
        $stmt->execute([
            $_POST['name'],
            $_POST['surname'],
            $_POST['patronymic'] ?? '',
            $_POST['gender'],
            $_POST['telephone'] ?? '',
            $_POST['dateofbirth'],
            $document_id
        ]);
        
        $pdo->commit();
        header("Location: index.php?success=1");
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Ошибка при добавлении пациента: " . $e->getMessage());
    }
}
?>