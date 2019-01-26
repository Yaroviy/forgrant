<?
include "./config.php";
include "./include/connect.php";

$str='<!DOCTYPE html><html prefix="og: https://ogp.me/ns#" lang="uk"><head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<SCRIPT language=JavaScript src="/js/popup_lib.js"></SCRIPT>
<SCRIPT language=JavaScript src="/js/dateselector.js"></SCRIPT>
<LINK href="/css/css.css" type=text/css rel=stylesheet>
<LINK href="/css/dateselector.css" type=text/css rel=stylesheet>

</head>
<body>
<div class="golovna">
';

function viborka_assoc($table,$vibrat,$uslovie)
{
	return @mysqli_fetch_assoc(mysqli_query($GLOBALS['link'], "select $vibrat from $table where $uslovie limit 1"));
}

function vyborka_tovariv()
{
	$sql="select * from goods where active=1";
	$vibor=mysqli_query($GLOBALS['link'],$sql);
	$dann=mysqli_num_rows($vibor);
	if($dann)
	{
		$str.='<table rules="all" border="1"><tr><th>Назва</th><th>Ціна</th><th>Дія</th></tr>'."\n";
		while(@$rw=mysqli_fetch_assoc($vibor))
		{
			$str.='<tr><td>'.$rw['nazva'].'</td><td><a href="'.$PHP_SELF.'?goods='.$rw['id'].'">'.$rw['cena'].'</a></td><td><a href="grafik.php?goods='.$rw['id'].'"><img src="/images/grafik.gif"></a></td></tr>';
		}
		$str.='</table>';
	}
	return $str;
}

function dodaty_akciu()
{
	$date=mktime(0,0,0,mb_substr($_POST['date'],3,2),mb_substr($_POST['date'],0,2),mb_substr($_POST['date'],6,4));
	$date1=mktime(0,0,0,mb_substr($_POST['date1'],3,2),mb_substr($_POST['date1'],0,2),mb_substr($_POST['date1'],6,4));
	$period=$date1-$date;
	//echo date('d.m.Y',$date).'!'.date('d.m.Y',$date1).'!';
	if(!$_POST['akciyaid'])
	$sql="insert into akcii set date_from='".$date."',date_to='".$date1."',period='".$period."',cena='".$_POST['cena']."',goods='".$_POST['goods']."',dodano='".time()."',active='1'";
	else
	$sql="UPDATE akcii set date_from='".$date."',date_to='".$date1."',period='".$period."',cena='".$_POST['cena']."' where id='".$_POST['akciyaid']."'";
	mysqli_query($GLOBALS['link'],$sql);
	Header("Location: index.php?goods=".$_GET['goods']);
	exit();
}

function vidalyty_akciu()
{
	$sql="delete from akcii where id='".$_GET['id']."'";
	mysqli_query($GLOBALS['link'],$sql);
	Header("Location: index.php?goods=".$_GET['goods']);
	exit();
}

function redactor_tovariv($redaguvaty)
{
	$sql="select * from akcii where active=1";
	$vibor=mysqli_query($GLOBALS['link'],$sql);
	$vb=viborka_assoc('goods','cena,nazva',"id=".$_GET['goods']);
	if($redaguvaty==1)
	$vv=viborka_assoc('akcii','id,date_from,date_to,cena',"id=".$_GET['id']);

	$str.='<h1>Існуючі акції на товар "'.$vb['nazva'].'" за стандартною ціною '.$vb['cena'].'грн</h1>';
	$str.='<form method="post" action="'.$PHP_SELF.'" name="calendar">
	<input name="date" onclick="popUpCalendar(this, calendar.date, \'dd.mm.yyyy\');" onchange="alert(&quot;Zapusk&quot;)" class="calendar_input"'.($vv?' value="'.date('d.m.Y',$vv['date_from']).'"':'').'>
	<input name="date1" onclick="popUpCalendar(this, calendar.date1, \'dd.mm.yyyy\');" onchange="alert(&quot;Zapusk&quot;)" class="calendar_input"'.($vv?' value="'.date('d.m.Y',$vv['date_to']).'"':'').'>
	<input name="cena" class="calendar_input" autocomplete="off"'.($vv?' value="'.$vv['cena'].'"':'').'>
	<input type="hidden" name="goods" value="'.$_GET['goods'].'">
	'.($vv?'<input type="hidden" name="akciyaid" value="'.$_GET['id'].'">':'').'
	<input type="submit" name="dodaty" value="'.($vv?'Редагувати акцію':'Додати акцію').'"></form><br />'."\n";

	$dann=mysqli_num_rows($vibor);
	if($dann)
	{		$str.='<table rules="all" border="1"><tr><th>Початок</th><th>Кінець</th><th>Період, дн</th><th>Ціна, грн</th><th>Дія</th></tr>'."\n";
        //echo getcwd();
		while(@$rw=mysqli_fetch_assoc($vibor))
		{
			$str.='<tr><td>'.date('d.m.Y',$rw['date_from']).'</td><td>'.date('d.m.Y',$rw['date_to']).'</td><td>'.ceil($rw['period']/86400).'</td><td>'.$rw['cena'].'</td><td><a href="'.$PHP_SELF.'?goods='.$_GET['goods'].'&type=1&id='.$rw['id'].'"><img src="/images/edit.gif"></a><a href="'.$PHP_SELF.'?goods='.$_GET['goods'].'&type=2&id='.$rw['id'].'" onclick="if(!confirm(\'Видалити '.$vb['nazva'].'?\'))return false;"><img src="/images/delete.gif"></a></td></tr>';
		}
		$str.='</table>';
	}

	$str.='<br /><br /><a href="index.php">Повернутись до товарів</a>';
	return $str;
}

if($_GET['goods'] and $_GET['type']==2)
{
	vidalyty_akciu();
}

if($_GET['goods'] and $_GET['type']==1)
{
	$redaguvaty=1;
}

if($_POST['dodaty'])
{	dodaty_akciu();}

if(!$_GET['goods'])
{
	$str.=vyborka_tovariv();
}
else
{
	$str.=redactor_tovariv($redaguvaty);
}

echo $str;

$str.='</div></body>';


?>