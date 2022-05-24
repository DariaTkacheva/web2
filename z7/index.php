<?php
header('Content-Type: text/html; charset=UTF-8');

  $user = 'u47541';
  $pass = '8900409';
  $db = new PDO('mysql:host=localhost;dbname=u47541', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
  
  function checkToken($token){
		return password_verify($_SESSION['token'], $token);
}

  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  if(!empty($_COOKIE['hack'])){
    $messages[]='<div class="error">Вас хотели взломать! Авторизуйтесь заново и избегайте перехода по ссылкам с неизвестных источников!</div>';
    setcookie('hack', '', 100000);
  }
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    $messages[] = 'Спасибо, результаты сохранены.<br>';
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
  }

  $errors = array();
  $errors['name'] = !empty($_COOKIE['name_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['date'] = !empty($_COOKIE['date_error']);
  $errors['pol'] = !empty($_COOKIE['pol_error']);
  $errors['konechn'] = !empty($_COOKIE['konechn_error']);
  $errors['super'] = !empty($_COOKIE['super_error']);
  $errors['info'] = !empty($_COOKIE['info_error']);
  $errors['check1'] = !empty($_COOKIE['check1_error']);

  if ($errors['name']) {
    setcookie('name_error', '', 100000);
    $messages['name_message'] = '<div class="error">Заполните имя.<br>Поле может быть заполнено символами только русского или только английского алфавитов</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages['email_message'] = '<div class="error">Заполните e-mail.<br>Поле может быть заполнено только символами английского алфавита, цифрами и знаком "@"</div>';
  }
  if ($errors['date']) {
    setcookie('date_error', '', 100000);
    $messages['date_message'] = '<div class="error">Выберите дату рождения</div>';
  }
  if ($errors['pol']) {
    setcookie('pol_error', '', 100000);
    $messages['pol_message'] = '<div class="error">Укажите ваш пол</div>';
  }
  if ($errors['konechn']) {
    setcookie('konechn_error', '', 100000);
    $messages['konechn_message'] = '<div class="error">Выберите количество конечностей</div>';
  }
  if ($errors['super']) {
    setcookie('super_error', '', 100000);
    $messages[] = '<div class="error">Выберите хотя бы одну сверхспособность</div>';
  }
  if ($errors['info']) {
    setcookie('info_error', '', 100000);
    $messages['info_message'] = '<div class="error">Введите информацию о себе</div>';
  }
  if ($errors['check1']) {
    setcookie('check1_error', '', 100000);
    $messages['check1_message'] = '<div class="error">Вы не можете отправить форму, не ознакомившись с контрактом</div>';
  }

  $values = array();
  $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['date'] = empty($_COOKIE['date_value']) ? '' : $_COOKIE['date_value'];
  $values['pol'] = empty($_COOKIE['pol_value']) ? '' : $_COOKIE['pol_value'];
  $values['konechn'] = empty($_COOKIE['konechn_value']) ? '' : $_COOKIE['konechn_value'];
  $values['super'] = [];
  $values['info'] = empty($_COOKIE['info_value']) ? '' : $_COOKIE['info_value'];
  $values['check1'] = empty($_COOKIE['check1_value']) ? '' : $_COOKIE['check1_value'];

  $super = array(
    '1' => "1",
    '2' => "2",
	'3' => "3",
	'4' => "4",
  );
  
if(!empty($_COOKIE['super_value'])) {
    $super_value = unserialize($_COOKIE['super_value']);
    foreach ($super_value as $s) {
      if (!empty($super[$s])) {
          $values['super'][$s] = $s;
      }
    }
  }

  if (!empty($_COOKIE[session_name()]) &&
  session_start() && !empty($_SESSION['login'])) {
    try{
      $sth = $db->prepare("SELECT id FROM users7 WHERE login = ?");
      $sth->execute(array($_SESSION['login']));
      $user_id = ($sth->fetchAll(PDO::FETCH_COLUMN, 0))['0'];
      $sth = $db->prepare("SELECT * FROM application7 WHERE id = ?");
      $sth->execute(array($user_id));
      $user_data = ($sth->fetchAll(PDO::FETCH_ASSOC))['0'];

      foreach ($user_data as $key=>$val){
        $values[$key] = $val;
      }
      $values['super'] = [];
      $super_value = unserialize($_COOKIE['super_value']);
        foreach ($super_value as $s) {
            if (!empty($super[$s])) {
                $values['super'][$s] = $s;
            }
        }

      } 
      catch(PDOException $e) {
          print($e->getMessage());
          exit();
      }
  }
  include('form.php');
}

else {
  $errors = FALSE;
// ИМЯ
if (empty($_POST['name'])) {
    setcookie('name_error', ' ', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else if(!preg_match("/^[а-яё]|[a-z]$/iu", $_POST['name'])){
    setcookie('name_error', htmlspecialchars($_POST['name']), time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('name_value', htmlspecialchars($_POST['name']), time() + 30 * 24 * 60 * 60);
  }
  // EMAIL
  if (empty($_POST['email'])){
    setcookie('email_error', ' ', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else if(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+.[a-zA-Z.]{2,5}$/", $_POST['email'])){
    setcookie('email_error', htmlspecialchars($_POST['email']), time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('email_value', htmlspecialchars($_POST['email']), time() + 30 * 24 * 60 * 60);
  }

  // Дата
  if ($_POST['date']=='') {
    setcookie('date_error', ' ', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('date_value', $_POST['date'], time() + 30 * 24 * 60 * 60);
  }

  // ПОЛ
  if (empty($_POST['pol'])) {
    setcookie('pol_error', ' ', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else{
  setcookie('pol_value', $_POST['pol'], time() + 30 * 24 * 60 * 60);
  }

  // КОНЕЧНОСТИ
  if (empty($_POST['konechn'])) {
    setcookie('konechn_error', ' ', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('konechn_value', $_POST['konechn'], time() + 30 * 24 * 60 * 60);
  }

  // СВЕРХСПОСОБНОСТИ
  $super=array();
  if(empty($_POST['super'])){
    setcookie('super_error', ' ', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else{
    foreach ($_POST['super'] as $key => $value) {
      $super[$key] = $value;
    }
    setcookie('super_value', serialize($super), time() + 30 * 24 * 60 * 60);
  }

  // ИНФОРМАЦИЯ О СЕБЕ
  if (empty($_POST['info'])) {
    setcookie('info_error', ' ', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('info_value', htmlspecialchars($_POST['info']), time() + 30 * 24 * 60 * 60);
  }

  // СОГЛАСИЕ
  if (empty($_POST['check1'])) {
    setcookie('check1_error', ' ', time() + 24 * 60 * 60);
    setcookie('check1_value', '', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('check1_value', $_POST['check1'], time() + 30 * 24 * 60 * 60);
  }

  if ($errors) {
    header('Location: index.php');
    exit();
  }
  else {
    setcookie('name_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('date_error', '', 100000);
    setcookie('pol_error', '', 100000);
    setcookie('konechn_error', '', 100000);
    setcookie('super_error', '', 100000);
    setcookie('info_error', '', 100000);
    setcookie('check1_error', '', 100000);
  }
  
  $token = $_POST['token']? $_POST['token'] : '';
  if(!checkToken($token)&&!empty($_COOKIE[session_name()])){
    setcookie('hack', 1);
    setcookie('name_value', '', 100000);
    setcookie('email_value', '', 100000);
    setcookie('date_value', '', 100000);
    setcookie('pol_value', '', 100000);
    setcookie('konechn_value', '', 100000);
    setcookie('super_value', '', 100000);
    setcookie('info_value', '', 100000);
    setcookie('check1_value', '', 100000);
    header("Location: login.php?do=logout"); 
    exit;
  }

 if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    try {
      $stmt = $db->prepare("SELECT id FROM users7 WHERE login =?");
      $stmt -> execute(array($_SESSION['login'] ));
      $user_id = ($stmt->fetchAll(PDO::FETCH_COLUMN))['0'];

      $stmt = $db->prepare("UPDATE application7 SET name = ?, email = ?, date = ?, pol = ?, konechn = ?, info = ? WHERE id =?");
      $stmt -> execute(array(
          $_POST['name'],
          $_POST['email'],
          $_POST['date'],
          $_POST['pol'],
          $_POST['konechn'],
          $_POST['info'],
          $user_id,
      ));
      $sth = $db->prepare("DELETE FROM Superpowers7 WHERE id = ?");
      $sth->execute(array($user_id));
      $stmt = $db->prepare("INSERT INTO Superpowers7 SET id = ?, superpowers = ?");
      foreach($_POST['super'] as $s){
          $stmt -> execute(array(
            $user_id,
            $s,
          ));
        }
      }
    catch(PDOException $e){
      print('Error: ' . $e->getMessage());
      exit();
    }
  }
  else {
    $sth = $db->prepare("SELECT login FROM users7");
    $sth->execute();
    $login_array = $sth->fetchAll(PDO::FETCH_COLUMN);
    $flag=true;
    do{
      $login = rand(1,1000);
      $pass = rand(1,10000);
      foreach($login_array as $key=>$value){
        if($value == $login)
          $flag=false;
      }
    }while($flag==false);
    $hash = password_hash((string)$pass, PASSWORD_BCRYPT);
    setcookie('login', $login);
    setcookie('pass', $pass);

    try {
      $stmt = $db->prepare("INSERT INTO application7 SET name = ?, email = ?, date = ?, pol = ?, konechn = ?, info = ?");
      $stmt -> execute(array(
          $_POST['name'],
          $_POST['email'],
          $_POST['date'],
          $_POST['pol'],
          $_POST['konechn'],
          $_POST['info'],
        )
      );

      $id_db = $db->lastInsertId();
      $stmt = $db->prepare("INSERT INTO Superpowers7 SET id = ?, superpowers = ?");
      foreach($_POST['super'] as $s){
          $stmt -> execute(array(
            $id_db,
            $s,
          ));
        }
      $stmt = $db->prepare("INSERT INTO users7 SET login = ?, pass = ?");
      $stmt -> execute(array(
          $login,
          $hash,
        )
      );
    }
    catch(PDOException $e){
      print('Error: ' . $e->getMessage());
      exit();
    }
  }
  
  setcookie('save', '1');
  header('Location: index.php');
}