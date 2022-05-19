<?php/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/
header('Content-Type: text/html; charset=UTF-8');// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
function change_pass($db){session_start(); // Начинаем сессию. //функция изменения пароля
  $login_ch = $_POST['user_login'];
  $old_pass = $_POST['old_pass'];
  $new_pass  =$_POST['new_pass'];

  $stmt = $db->prepare("SELECT * FROM users6 WHERE login = ?");// взяли логин и пароль
    $stmt->execute(array(
      $login_ch
    ));
  $user = $stmt->fetch();
  if (password_verify($old_pass, $user['pass'])) { // если старый пароль совпадает с паролем из бд, то заменяем в бд на новый пароль
    $stmt = $db->prepare("UPDATE users6 SET pass = ? WHERE login = ?");
      $stmt -> execute(array(
          password_hash($new_pass, PASSWORD_BCRYPT),
          $login_ch
      ));
      $_SESSION['login'] = $login_ch;
  }
  else{//обновление данных админа
    $stmt = $db->prepare("SELECT * FROM admin WHERE login = ?"); //взяли логин и пароль
    $stmt->execute(array(
      $login_ch
    ));
    $admin = $stmt->fetch();
    if ($old_pass==$admin['pass']||password_verify($old_pass, $admin['pass'])) { //если старый пароль совпадает с паролем из бд, то заменяем в бд на новый пароль для админа
      $stmt = $db->prepare("UPDATE admin SET pass = ? WHERE login = ?");
      $stmt -> execute(array(
          password_hash($new_pass, PASSWORD_BCRYPT),
          $login_ch
      ));
    }
    else {
      echo "Неверный логин или пароль";
    }
  }
  header('Location: login.php');
  exit();
}

session_start();// Начинаем сессию.

$db_user = 'u47541';
$db_pass = '8900409';
// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if(isset($_GET['do'])&&$_GET['do'] == 'logout'){
    session_start();    
    session_unset();
    session_destroy();
    setcookie ("PHPSESSID", "", time() - 3600, '/');
    header("Location: index.php");
    exit;
  }
  else if(isset($_GET['act'])&&$_GET['act'] == 'change_pass'){
    ?>
     <form action="" method="POST">
	   <h2>Смена пароля</h2>
       <p><label for="user_login">Логин </label><input name="user_login" /></p>
       <p><label for="old_pass">Старый пароль </label><input name="old_pass" /></p>
       <p><label for="new_pass">Новый пароль </label><input name="new_pass" /></p>
       <input type="submit" value="Изменить" />
     </form>
    <?php 
  }
  else{
    ?>
    <form action="" method="POST">
      <p><label for="login">Логин </label><input name="login" /></p>
      <p><label for="pass">Пароль </label><input name="pass" /></p>
      <input type="submit" value="Войти" />
    </form>
    <br><a href='login.php?act=change_pass'>Изменить пароль</a><br>
    <?php
  }
}

else {// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
	 // TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.
  $db = new PDO('mysql:host=localhost;dbname=u47541', $db_user, $db_pass, array(
    PDO::ATTR_PERSISTENT => true
  ));

  try {//авторизация за админа
    if(isset($_GET['act'])&&$_GET['act'] == 'change_pass'){
      change_pass($db);
    }
	  // Если все ок, то авторизуем пользователя.
    $login = $_POST['login'];
    $pass =  $_POST['pass'];
    
    $stmt = $db->prepare("SELECT * FROM users6 WHERE login = ?");
    $stmt->execute(array(
      $login
    ));
    $user = $stmt->fetch();
    if (password_verify($pass, $user['pass'])) { //верность введенного пароля
      $_SESSION['login'] = $login;
    }
    else{
      $stmt = $db->prepare("SELECT * FROM admin WHERE login = ?");
      $stmt->execute(array(
        $login
      ));
      $admin = $stmt->fetch();
      if ($pass==$admin['pass']||password_verify($pass, $admin['pass'])) { //проверка настоящего пароля админа
        header('Location: admin.php');
        exit();
      }
      else {
        echo "Неверный логин или пароль";
        exit();
      }
    }
  }
  catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    exit();
  }
  header('Location: ./');// Делаем перенаправление.
}
