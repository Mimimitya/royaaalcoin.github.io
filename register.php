
``php
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

        // Создаем реферальный код и добавляем пользователя в базу данных
        $referralCode = generateReferralCode($username);
        
        $sql = "INSERT INTO users (username, referral_code, referred_by) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $referralCode, $referredBy);
        
        if ($stmt->execute()) {
            $message = "Пользователь зарегистрирован! Ваш реферальный код: " . $referralCode;
        } else {
            $message = "Ошибка: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>
