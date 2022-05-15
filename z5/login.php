<?php/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

header('Content-Type: text/html; charset=UTF-8');// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.

session_start();// Начинаем сессию.

$db_user = 'u47541';
$db_pass = '8900409';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
    if(isset($_GET['do'])&&$_GET['do'] == 'logout'){// выход из профиля
    session_start();    
    session_unset();
    session_destroy();
    setcookie ("PHPSESSID", "", time() - 3600, '/');
    header("Location: index.php");
    exit;}
?>

<form action="" method="post">
  <p><label for="login">Логин </label><input name="login" /></p>
  <p><label for="pass">Пароль </label><input name="pass" /></p>
  <input type="submit" value="Войти" />
</form>

<?php
}
else {// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.

  $login = $_POST['login'];  // Если все ок, то авторизуем пользователя.
  $pass =  $_POST['pass'];
// TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.
  $db = new PDO('mysql:host=localhost;dbname=u47541', $db_user, $db_pass, array(
    PDO::ATTR_PERSISTENT => true
  ));

  try {
    $stmt = $db->prepare("SELECT * FROM users5 WHERE login = ?");
    $stmt->execute(array(
      $login
    ));
    $user = $stmt->fetch();
    if (password_verify($pass, $user['pass'])) {
      $_SESSION['login'] = $login;
    }
    else {
      echo "Неверный логин или пароль";
      exit();
    }

  }
  catch(PDOException $e) {
    echo 'Ошибка: ' . $e->getMessage();
    exit();
  }
  header('Location: ./'); // Делаем перенаправление.
}
