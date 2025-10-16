<!DOCTYPE html>
<html>
<head>
    <title>Сброс пароля</title>
</head>
<body>
<p>Привет, {{ $user->name }}</p>
<p>Вы запросили сброс пароля для вашего аккаунта.</p>
<p>Для установки нового пароля нажмите на кнопку ниже:</p>
<p>
<a href="{{ $resetUrl }}"
   style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
    Сбросить пароль
</a>
</p>
<p>Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.</p>
<p>Ссылка действительна в течение 60 минут.</p>
<p></p>

</body>
</html>
