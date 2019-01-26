<?php
global $link,$setting;
@$link=mysqli_connect($db_host,$db_user,$db_passwd,$db_name) or die("Вибачте, через технічні проблеми нема зв'язку з базой. Оновіть сторінку трохи пізніше, ми працюємо над вирішенням проблеми...");
if (mysqli_connect_errno()) {
    echo("Вибачте, через технічні проблеми нема зв'язку з базой. Оновіть сторінку трохи пізніше, ми працюємо над вирішенням проблеми...");
    exit();
}
mysqli_set_charset($link, "utf8");

$sql="select perem,znach from setting";
$vb=mysqli_query($link,$sql);
while(@$rw=mysqli_fetch_assoc($vb))
{
	$setting[$rw['perem']]=$rw['znach'];
}

?>