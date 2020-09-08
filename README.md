# PHP-Form-Sites-Connector
Файл **functions.php** должен подключаться к любому вызываемому html/php файлу, что прописано в **.htaccess**<br>
Если автоматическое подключение на каком-либо из файлов не сработало, **functions.php** следует подключить вручную:<br>
``` <?php if (file_exists("functions.php")) { require_once("functions.php"); }?>```
