<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">
</head>

<?php
$db_user = 'u47541';
$db_pass = '8900409';
$db = new PDO('mysql:host=localhost;dbname=u47541', $db_user, $db_pass, array(
    PDO::ATTR_PERSISTENT => true
));
$login = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
$stmt = $db->prepare("SELECT * FROM admin WHERE login = ?");
      $stmt->execute(array(
        $login
      ));
$admin_pass = $stmt->fetch();

if (empty($_SERVER['PHP_AUTH_USER']) ||
 empty($_SERVER['PHP_AUTH_PW']) ||
 $_SERVER['PHP_AUTH_USER'] != 'admin' ||
 $_SERVER['PHP_AUTH_PW'] != '123') {
 header('HTTP/1.1 401 Unauthorized');
 header('WWW-Authenticate: Basic realm="My site"');
 header('Content-Type: text/html; charset=UTF-8');
 print '<h1>401 Требуется авторизация</h1>';
      exit();
}

print('Вы успешно авторизовались и видите защищенные паролем данные.<br>');

function show_tables($db){
  $sql = 'SELECT  application7.*, 
                    SuperDef.name as power,
                    users7.login
            FROM application7
            INNER JOIN Superpowers7
                ON application7.id = Superpowers7.id
            INNER JOIN SuperDef 
                ON Superpowers7.superpowers = SuperDef.id
            INNER JOIN users7 
                ON users7.id = application7.id;';
  ?>
  <table class="table">
  <caption>Data of users</caption> 
    <tr><th>id</th><th>name</th><th>email</th><th>date</th><th>pol</th><th>konechn</th><th>info</th><th>superpower</th><th>login</th><th colspan="3">action</th></tr>
  <?php
	  foreach ($db->query($sql, PDO::FETCH_ASSOC) as $row) {
      print('<tr>');
      foreach ($row as $v){
        print('<td>'.htmlspecialchars($v). '</td>');
      }
      print('<td colspan="2"> <a href="?act=edit_article&edit_id='.$row["id"].'">edit</a></td>   ');print('<td> <a href="?act=delete_article&delete_id='.$row["id"].'">delete</a></td>');
    } 
    print('</tr></table>');
    print('<td> <a href="?act=add_article">add</a></td><br>');

    
    $sql = 'SELECT SuperDef.name as superpower, COUNT(superpowers) as number_of_users
            FROM Superpowers7 
            LEFT JOIN SuperDef
            ON Superpowers7.superpowers=SuperDef.id
            GROUP BY superpowers;';
    ?>
    <table class="table">
    <caption>Statistics of Superpowers</caption> 
      <tr><th>superpower</th><th>number_of_users</th></tr>
    <?php
    foreach ($db->query($sql, PDO::FETCH_ASSOC) as $row) {
      print('<tr>');
      foreach ($row as $k=>$v){
	  	  print('<td>'.$v. '</td>');
      }
    }print('</tr></table>');
    print('<br><a href=login.php?do=logout> Выход</a><br>');
}
function form($db){
  ?>
  <label for='name'>Имя</label>
      <input name='name'><br />
  <label for='email'>Email</label>
      <input name='email'><br />
  <label for='date'>Дата рождения</label>
      <input name='date'><br />
  <label>Пол:</label>
	  <input type="radio" value="male" name='pol'>Мужской
	  <input type="radio" value="female" name='pol'>Женский<br />
  
  <label>Количество конечностей:</label>
	  <input type="radio" name='konechn' value='1'>1
	  <input type="radio" name='konechn' value='2'>2
	  <input type="radio" name='konechn' value='3'>3
	  <input type="radio" name='konechn' value='4'>4<br />
	  
	  <p>Сверхспособности:</p>
      <label><select name="super[]" multiple="multiple">
        <?php
				$sql = 'SELECT * FROM SuperDef';
				foreach ($db->query($sql) as $row) {
					?><option value=<?php print $row['id']?> name=super[]>
					<?php print $row['name'] . "\t";
				}
			?></option>
		</select>
      </label><br />
	  
	  
	  
  <label for="info">Информация о себе</label>
     <textarea name='info'></textarea><br />

  <?php
}
function errors(){
  $errors = FALSE;
    // ИМЯ
    if (empty($_POST['name'])&&empty($_GET['edit_id'])) {
      $errors = TRUE;
    }
    else if(!empty($_POST['name'])&&!preg_match("/^[а-яё]|[a-z]$/iu", $_POST['name'])){
      $errors = TRUE;
    }
    // EMAIL
    if (empty($_POST['email'])&&empty($_GET['edit_id'])){
      $errors = TRUE;
    }
    else if(!empty($_POST['email'])&&!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+.[a-zA-Z.]{2,5}$/", $_POST['email'])){
      $errors = TRUE;
    }
    // Дата
    if (empty($_POST['date'])&&empty($_GET['edit_id'])){
      $errors = TRUE;
    }
    // ПОЛ
    if (empty($_POST['pol'])&&empty($_GET['edit_id'])) {
      $errors = TRUE;
    }
    // КОНЕЧНОСТИ
    if (empty($_POST['konechn'])&&empty($_GET['edit_id'])) {
      $errors = TRUE;
    }
    // СВЕРХСПОСОБНОСТИ
    $super=array();
    if(empty($_POST['super'])&&empty($_GET['edit_id'])){
      $errors = TRUE;
    }
    else if(!empty($_POST['super'])){
      foreach ($_POST['super'] as $key => $value) {
        $super[$key] = $value;
      }
    }
    // ИНФОРМАЦИЯ О СЕБЕ
    if (empty($_POST['info'])&&empty($_GET['edit_id'])) {
      $errors = TRUE;
    }
    if ($errors) {
      return true;
    }
    else{
      return false;
    }
}
function delete_user($db, $del){
  try{
    $sth = $db->prepare("DELETE FROM application7 WHERE id = ?");
    $sth->execute(array($del));
    $sth = $db->prepare("DELETE FROM Superpowers7 WHERE id = ?");
    $sth->execute(array($del));
    $sth = $db->prepare("DELETE FROM users7 WHERE id = ?");
    $sth->execute(array($del));
  }
  catch(PDOException $e){
    print('Error: ' . $e->getMessage());
    exit();
  }
}
function add_user($db){
  $sth = $db->prepare("SELECT login FROM users7");
  $sth->execute();
  $login_array = $sth->fetchAll(PDO::FETCH_COLUMN);
  $flag=true;
  do{
    $login = rand(1,1000);
    $pass = rand(1,10000);
    foreach($login_array as $value){
      if($value == $login)
        $flag=false;
    }
  }while($flag==false);
  $hash = password_hash((string)$pass, PASSWORD_BCRYPT);
  
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

    $id_db = $db->lastInsertId("application7");
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
function edit_user($db, $edit){
  try {
    $stmt = $db->prepare('SELECT * FROM application7 WHERE id=?');
    $stmt -> execute(array($edit));
    $a = array();
    $old_data = ($stmt->fetchAll(PDO::FETCH_ASSOC))['0'];
    foreach ($old_data as $key=>$val){
      $a[$key] = $val;
    }
    $name = empty($_POST['name']) ? $a['name'] : $_POST['name'];
    $email = empty($_POST['email']) ? $a['email'] : $_POST['email'];
    $date = empty($_POST['date']) ? $a['date'] : $_POST['date'];
    $pol = empty($_POST['pol']) ? $a['pol'] : $_POST['pol'];
    $konechn = empty($_POST['konechn']) ? $a['konechn'] : $_POST['konechn'];
    $info = empty($_POST['info']) ? $a['info'] : $_POST['info'];

    $stmt = $db->prepare("UPDATE application7 SET name = ?, email = ?, date = ?, pol = ?, konechn = ?, info = ? WHERE id =?");
    $stmt -> execute(array(
        $name,
        $email,
        $date,
        $pol,
        $konechn,
        $info,
        $edit
    ));
    if(!empty($_POST['super'])){
      $sth = $db->prepare("DELETE FROM Superpowers7 WHERE id = ?");
      $sth->execute(array($edit));
      $stmt = $db->prepare("INSERT INTO Superpowers7 SET id = ?, superpowers = ?");
      foreach($_POST['super'] as $s){
          $stmt -> execute(array(
            $edit,
            $s,
          ));
        }
    }
  }
  catch(PDOException $e){
    print('Error: ' . $e->getMessage());
    exit();
  }
}

if($_SERVER['REQUEST_METHOD'] == 'GET'){
  show_tables($db);
  if(isset($_GET['act'])&&$_GET['act']=='edit_article'){
    ?><form action="" method="post">
      <h4>Редактировать профиль с id=<?php print($_GET['edit_id']); ?></h4>
    <p>
      <?php form($db);?>
    </p>
    <p><button type="submit" value="send">Отправить</button></p>
    </form>
    <?php
  }
  else if(isset($_GET['act'])&&$_GET['act']=='add_article'){
    ?><form action="" method="post">
      <h5>Добавить профиль</h5>
    <p>
      <?php form($db);?>
    </p>
    <p><button type="submit" value="send">Отправить</button></p>
    </form>
    <?php
     }
  else if(isset($_GET['act'])&&$_GET['act']=='delete_article'){
    ?>
    <form action="" method="post">
    <h4>Удалить пользователя c id=<?php print($_GET['delete_id']);?>?</h4>
    <p><button type="submit" value="send">Ок</button></p>
    </form>
    <?php
    }
}
else{
  try {
    if(!empty($_GET['delete_id'])){delete_user($db, $_GET['delete_id']);}
    if(!empty($_GET['edit_id']))if(!errors()){edit_user($db, $_GET['edit_id']);}
    if(isset($_GET['act'])&&$_GET['act']=='add_article'){add_user($db);}
  }
  catch(PDOException $e) {
    echo 'Ошибка: ' . $e->getMessage();
    exit();
  }
  header('Location: admin.php');
}