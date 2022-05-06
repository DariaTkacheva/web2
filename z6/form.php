<!DOCTYPE html>

<html lang="ru">

  <head>
    <meta charset="UTF-8">
    <title>Задание 6</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
  <?php 
		if (!empty($messages)) {
			if(isset($messages['save'])) print('<div id="messages" class="ok">'); else print('<div id="messages">');
			foreach ($messages as $message) {
				print($message);
			}
		  print('</div>');
		}
	?>
   <a href="login.php">Вход для пользователя</a><br>
   <a href="admin.php">Вход для админа</a>
   <div id="form">
    	<h1><b>Форма</b></h1>
	<form action="" method="POST">
	
      <label for="name"></label>
        <b>Введите имя:</b><br />
        <input type="text" name="name" <?php if (!empty($errors['name'])) {print 'class="error"';} ?> <?php if(empty($errors['name'])&&!empty($values['name'])) print 'class="ok"';?> value="<?php isset($_COOKIE['name_error'])? print trim($_COOKIE['name_error']) : print $values['name']; ?>">
      <br />
	  
      <label for="email"></label>
        <b>Введите свой email:</b><br />
        <input type="text" id="email" name="email" <?php if(!empty($errors['email']))  print 'class="error"';?> <?php if(empty($errors['email'])&&!empty($values['email'])) print 'class="ok"';?> value="<?php isset($_COOKIE['email_error'])? print trim($_COOKIE['email_error']) : print $values['email']; ?>">
      <br />
	  
      <label for="date"><b>Дата рождения:</b></label><br />
        <input type="date" id="date" name="date" <?php if(!empty($errors['date']))  print 'class="error"';?> <?php if(empty($errors['date'])&&!empty($values['date'])) print 'class="ok"';?> value="<?php isset($_COOKIE['date_error'])? print trim($_COOKIE['date_error']) : print $values['date']; ?>">
      <br />
	  
	  <label <?php if(!empty($errors['pol'])) print 'class="error_check"'?>>
      <b>Пол:</b></label><br />
      <input type="radio" id="male"name="pol" value="male" <?php if (isset($values['pol'])&&$values['pol'] == 'male') print("checked"); ?>>мужской
      <input type="radio" id="female" name="pol" value="female" <?php if (isset($values['pol'])&&$values['pol'] == 'female') print("checked"); ?>>женский<br />
	  
	  <label <?php if(isset($_COOKIE['konechn_error'])) print 'class="error_check"'?>><b>Количество конечностей:</b></label><br />
      <input type="radio" id="1" name="konechn" value="1" <?php if (isset($values['konechn'])&&$values['konechn'] == '1') print("checked"); ?>>1
	  <input type="radio" id="2" name="konechn" value="2" <?php if (isset($values['konechn'])&&$values['konechn'] == '2') print("checked"); ?>>2
	  <input type="radio" id="3" name="konechn" value="3" <?php if (isset($values['konechn'])&&$values['konechn'] == '3') print("checked"); ?>>3
      <input type="radio" id="4" name="konechn" value="4" <?php if (isset($values['konechn'])&&$values['konechn'] == '4') print("checked"); ?>>4<br />	
	  
	  <b>Сверхспособности:</b><br />
      <label><select name="super[]" multiple="multiple" <?php if (!empty($errors['super'])) print 'class="error"';?>>
        <?php
				$sql = 'SELECT * FROM SuperDef';
				foreach ($db->query($sql) as $row) {
					?><option value=<?php print $row['id']?> name=super[] <?php if(isset($values['super'][$row['id']])&&empty($_COOKIE['super_error']))print("checked"); print "\t"; ?>>
					<?php print $row['name'] . "\t";
				}
			?></option>
		</select>
      </label><br />
	  
	  <label for="info"><b>Информация о себе:</b></label><br />
        <textarea id="info" name="info" <?php if(!empty($errors['info']))  print 'class="error"';?> <?php if(empty($errors['info'])&&!empty($values['info'])) print 'class="ok"';?>><?php isset($_COOKIE['info_error']) ? print trim($_COOKIE['info_error']) : print $values['info'] ?></textarea>
      <br />
	  
	  <label <?php if(!empty($errors['check1'])) print 'class="error_check"'?>><b>С контрактом ознакомлен (а)</b></label>
      <input type="checkbox" id="check1" name="check1" value="check1" <?php if (isset($values['check1'])&&$values['check1'] == 'check1') print("checked"); ?>><br />
	  
      <p><button type="submit" value="send">Отправить</p>
    </form>
	<?php if(!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) print( '<div id="footer">Вход с логином ' . $_SESSION["login"]. '<br> <a href=login.php?do=logout> Выход</a><br></div>');?>
    </div>
  </body>
</html>