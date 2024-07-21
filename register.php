<?php
$host = 'localhost'; // Хост базы данных
$db   = 'clicker_game'; // Имя базы данных
$user = 'root'; // Имя пользователя
$pass = ''; // Пароль
$message = '';

// Подключение к базе данных
$conn = new mysqli($host, $user, $pass, $db);

// Проверьте соединение
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

function generateReferralCode($username) {
    return substr(md5($username . time()), 0, 10); // Генерация реферального кода
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'])) {
        // Обработка регистрации пользователя
        $username = $_POST['username'];
        $referredBy = $_POST['referral_code'] ?? null; // Получаем реферальный код

        // Создаем реферальный код
        $referralCode = generateReferralCode($username);

        // Добавляем пользователя в базу данных
        $sql = "INSERT INTO users (username, referral_code, referred_by) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $referralCode, $referredBy);

        if ($stmt->execute()) {
            $message = "Пользователь зарегистрирован! Ваш реферальный код: " . $referralCode;

            // Добавляем бонус пользователю, у кого был реферрер (если есть)
            if ($referredBy) {
                $sql_bonus = "UPDATE users SET coins = coins + 10000 WHERE referral_code = ?";
                $stmt_bonus = $conn->prepare($sql_bonus);
                $stmt_bonus->bind_param("s", $referredBy);
                $stmt_bonus->execute();
                $stmt_bonus->close();
            }
        } else {
            $message = "Ошибка: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
