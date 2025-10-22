CREATE DATABASE MedicalCenter;



-- Таблица логов (должна быть создана первой из-за внешних ключей)
CREATE TABLE log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lastentry DATETIME,
    ip VARCHAR(45)
);

-- Вспомогательные таблицы документов
CREATE TABLE passport (
    id INT PRIMARY KEY AUTO_INCREMENT,
    series VARCHAR(10),
    number VARCHAR(20),
    issued_by TEXT,
    issue_date DATE,
    department_code VARCHAR(10)
);

CREATE TABLE medical_card (
    id INT PRIMARY KEY AUTO_INCREMENT,
    card_number VARCHAR(50) UNIQUE,
    issue_date DATE,
);

CREATE TABLE snils (
    id INT PRIMARY KEY AUTO_INCREMENT,
    number VARCHAR(14) UNIQUE,
    issue_date DATE
);

CREATE TABLE dms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    policy_number VARCHAR(50) UNIQUE,
    insurance_company VARCHAR(255),
    start_date DATE,
    end_date DATE
);

-- Основная таблица документов
CREATE TABLE document (
    id INT PRIMARY KEY AUTO_INCREMENT,
    passport_id INT,
    medical_card_id INT,
    snils_id INT,
    dms_id INT,
    FOREIGN KEY (passport_id) REFERENCES passport(id),
    FOREIGN KEY (medical_card_id) REFERENCES medical_card(id),
    FOREIGN KEY (snils_id) REFERENCES snils(id),
    FOREIGN KEY (dms_id) REFERENCES dms(id)
);

-- Таблица профилей
CREATE TABLE profile (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    surname VARCHAR(128) NOT NULL,
    patronymic VARCHAR(128),
    gender ENUM('мужской', 'женский'),
    telephone VARCHAR(20),
    dateofbirth DATE,
    dataregistration DATETIME DEFAULT CURRENT_TIMESTAMP,
    document_id INT,
    FOREIGN KEY (document_id) REFERENCES document(id)
    role ENUM('Администратор','Доктор','Юзер') DEFAULT 'Юзер',
);

-- Таблица пользователей
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(128) NOT NULL UNIQUE,
    hash_password VARCHAR(255) NOT NULL,
    log_id INT,
    profile_id INT,
    verified BOOLEAN DEFAULT FALSE
    FOREIGN KEY (log_id) REFERENCES log(id),
    FOREIGN KEY (profile_id) REFERENCES profile(id)
);

-- Таблица отделений медицинского центра
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    floor INT,
    phone_extension VARCHAR(10),
    head_doctor_id INT
);

-- Таблица врачей/специалистов
CREATE TABLE doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    profile_id INT NOT NULL,
    specialization VARCHAR(255) NOT NULL,
    department_id INT,
    room_number VARCHAR(20),
    work_schedule TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (profile_id) REFERENCES profile(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Добавляем внешний ключ для заведующего отделением
ALTER TABLE departments ADD FOREIGN KEY (head_doctor_id) REFERENCES doctors(id);

-- Таблица медицинских услуг
CREATE TABLE medical_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('консультация', 'диагностика', 'лечение', 'лаборатория', 'процедура'),
    duration_minutes INT,
    cost DECIMAL(10,2)
);

-- Таблица диагнозов по МКБ-10
CREATE TABLE diagnoses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    icd_code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(500) NOT NULL,
    description TEXT
);

-- Основная таблица медицинской истории
CREATE TABLE medical_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medical_card_id INT NOT NULL,
    visit_date DATETIME NOT NULL,
    doctor_id INT NOT NULL,
    department_id INT NOT NULL,
    complaint TEXT COMMENT 'Жалобы пациента',
    anamnesis TEXT COMMENT 'Анамнез',
    objective_status TEXT COMMENT 'Объективный статус',
    diagnosis_id INT COMMENT 'Основной диагноз',
    treatment_plan TEXT COMMENT 'План лечения',
    recommendations TEXT COMMENT 'Рекомендации',
    next_visit_date DATE COMMENT 'Дата следующего визита',
    visit_type ENUM('первичный', 'повторный', 'профилактический', 'экстренный') DEFAULT 'первичный',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medical_card_id) REFERENCES medical_card(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (diagnosis_id) REFERENCES diagnoses(id)
);

-- Таблица назначений
CREATE TABLE prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medical_history_id INT NOT NULL,
    service_id INT,
    medication_name VARCHAR(255),
    dosage VARCHAR(100),
    frequency VARCHAR(100),
    duration_days INT,
    instructions TEXT,
    prescription_date DATE,
    status ENUM('назначено', 'выполнено', 'отменено') DEFAULT 'назначено',
    FOREIGN KEY (medical_history_id) REFERENCES medical_history(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES medical_services(id)
);

-- Таблица результатов анализов и обследований
CREATE TABLE examination_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medical_history_id INT NOT NULL,
    examination_type VARCHAR(255) NOT NULL,
    result_text TEXT,
    result_value DECIMAL(10,2),
    unit VARCHAR(50),
    normal_range VARCHAR(100),
    conclusion TEXT,
    performed_date DATE,
    performed_by INT COMMENT 'Кто выполнил исследование',
    FOREIGN KEY (medical_history_id) REFERENCES medical_history(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES doctors(id)
);

-- Таблица расписания врачей
CREATE TABLE doctor_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    work_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    slot_duration INT DEFAULT 30 COMMENT 'Длительность слота в минутах',
    is_working_day BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);

-- Таблица записей на прием
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medical_card_id INT NOT NULL,
    doctor_id INT NOT NULL,
    schedule_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('запланирован', 'подтвержден', 'отменен', 'завершен') DEFAULT 'запланирован',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    cancellation_reason TEXT,
    FOREIGN KEY (medical_card_id) REFERENCES medical_card(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (schedule_id) REFERENCES doctor_schedule(id)
);
