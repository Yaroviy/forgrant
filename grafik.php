<?
include "./config.php";
include "./include/connect.php";


$str='<!doctype html>

<SCRIPT src="js/ChartNew.js"></script>


<SCRIPT>

function setColor(area,data,config,i,j,animPct,value)
{
  if(value > 35)return("rgba(220,0,0,"+animPct);
  else return("rgba(0,220,0,"+animPct);

}

var charJSPersonnalDefaultOptions = { decimalSeparator : "," , thousandSeparator : ".", roundNumber : "none", graphTitleFontSize: 2 };

defCanvasWidth=1600;
defCanvasHeight=600;
';

function viborka_assoc($table,$vibrat,$uslovie)
{
	return @mysqli_fetch_assoc(mysqli_query($GLOBALS['link'], "select $vibrat from $table where $uslovie limit 1"));
}

function result($table,$vibrat,$uslovie,$link=0)
{
    if(!$link)
    $link=$GLOBALS['link'];
    $r=mysqli_fetch_row(mysqli_query($link, "select $vibrat from $table where $uslovie limit 1"));
    //if($_SERVER[REMOTE_ADDR]=='134.249.188.174')echo"select $vibrat from $table where $uslovie limit 1!$r[0]!\n<br>";
	return $r[0];
}


$datearr=array(1,0);
$colors=array('2015'=>'green','2016'=>'red','2017'=>'yellow','2018'=>'blue');
$colors1=array('2015'=>'red','2016'=>'green','2017'=>'blue','2018'=>'yellow');


function stvorennya_ciny($goods,$date,$sort)
{
    $vb=viborka_assoc('goods','cena',"id=".$date);
	$sql="select * from akcii where goods='".$goods."' and date_from<=".$date." and date_to>=".$date.' order by '.($sort==2?'date_from desc':'period').' limit 1';
	//echo date('d.m.Y',$date)."!sort=$sort!$sql!\n<br />";


	$vibor=mysqli_query($GLOBALS['link'],$sql);
	$dann=mysqli_num_rows($vibor);
	if($dann)
	{
		while(@$rw=mysqli_fetch_assoc($vibor))
		{
			//echo'Цена на '.date('d.m.Y',$date).' = '.$rw['cena']."\n<br />";
			$str=$rw['cena'];
		}
	}
	else
	$str=result('goods','cena',"id=".$goods);

	//$graph['']

	return $str;
}

for($y=2015;$y<2019;$y++)
{
	for($m=1;$m<13;$m++)
	{
		foreach($datearr as $key => $d)
		{
			$date=mktime(0,0,0,($d?$m:$m+1),$d,$y);
			if($y==2015)
			{
				$labels.='"'.date('d.m',$date).'",';
			}
			$dannye[$y].=stvorennya_ciny($_GET['goods'],$date,1).',';
			$dannye1[$y].=stvorennya_ciny($_GET['goods'],$date,2).',';
		}
	}
}


foreach($dannye as $key => $value)
{
	$graph.='
	{
		strokeColor : "'.$colors1[$key].'",
		pointColor : "'.$colors[$key].'",
		pointstrokeColor : "yellow",
		data : ['.mb_substr($value,0,-1).'],
      	title : "'.$key.'"
	},';
}

foreach($dannye1 as $key => $value)
{
	$graph1.='
	{
		strokeColor : "'.$colors1[$key].'",
		pointColor : "'.$colors[$key].'",
		pointstrokeColor : "yellow",
		data : ['.mb_substr($value,0,-1).'],
      	title : "'.$key.'"
	},';
}

$str.='
var mydata1 = {
	labels : ['.mb_substr($labels,0,-1).'],
	datasets : [
	'.mb_substr($graph,0,-1).'
	]
}
	';

$str.='
var mydata2 = {
	labels : ['.mb_substr($labels,0,-1).'],
	datasets : [
	'.mb_substr($graph1,0,-1).'
	]
}
	';


$nazva_tovaru=result('goods','nazva',"id=".$_GET['goods']);

$str.='
var startWithDataset =1;
var startWithData =1;

var opt1 = {
      animationStartWithDataset : startWithDataset,
      animationStartWithData : startWithData,
      animationSteps : 200,
      canvasBorders : true,
      canvasBordersWidth : 3,
      canvasBordersColor : "black",
      graphTitle : "Пріоритетна ціна з меншим періодом дії",
      legend : true,
      inGraphDataShow : true,
      annotateDisplay : true,
      graphTitleFontSize: 18

}

var opt2 = {
      animationStartWithDataset : startWithDataset,
      animationStartWithData : startWithData,
      animationSteps : 200,
      canvasBorders : true,
      canvasBordersWidth : 3,
      canvasBordersColor : "black",
      graphTitle : "Пріоритетна ціна, встановлена пізніше (за датою старта)",
      legend : true,
      inGraphDataShow : true,
      annotateDisplay : true,
      graphTitleFontSize: 18

}


</SCRIPT>


<html>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<head>
		<title>Графік за товаром "'.$nazva_tovaru.'"</title>
	</head>
	<body>

  <center>
    <FONT SIZE=6><B>Графіки за товаром "'.$nazva_tovaru.'"</B></FONT>    <BR>

    <script>

    document.write("<canvas id=\"canvas_Line1\" height=\""+defCanvasHeight+"\" width=\""+defCanvasWidth+"\"></canvas>");
    document.write("<canvas id=\"canvas_Line2\" height=\""+defCanvasHeight+"\" width=\""+defCanvasWidth+"\"></canvas>");
window.onload = function() {
    var myLine = new Chart(document.getElementById("canvas_Line1").getContext("2d")).Line(mydata1,opt1);
    var myLine = new Chart(document.getElementById("canvas_Line2").getContext("2d")).Line(mydata2,opt2);
}
    </script>
  </body>
</html>
';

echo $str;

?>