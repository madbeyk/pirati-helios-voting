<?
header('Content-type: text/html; charset=utf-8');

echo "<a href='./cf2019.php?kraj=jihocesky'>Tak jo ...</a>";
exit();

include_once('simple_html_dom.php'); 

$clenu_total=892;
$timelimit=120;

$clenove=array();
$helios=array(
'lidr_1_kolo'=>array('name'=>'EP - Lídr kandidátky<br/>1. kolo','file'=>'cf_24_2018_volba_lidra_kandidátky_do_EP_1_kolo.txt','votes'=>465,'finished'=>true),
'lidr_2_kolo'=>array('name'=>'EP - Lídr kandidátky<br/>2. kolo','file'=>'cf_24_2018_volba_lidra_kandidátky_do_EP_2_kolo.txt','votes'=>449,'finished'=>true),
'celo_1_kolo'=>array('name'=>'EP - Čelo kandidátky<br/>1. kolo','file'=>'cf_24_2018_volba_cela_kandidátky_do_EP_1_kolo.txt','votes'=>454,'finished'=>true),
'celo_2_kolo'=>array('name'=>'EP - Čelo kandidátky<br/>2. kolo','file'=>'cf_24_2018_volba_cela_kandidátky_do_EP_2_kolo.txt','votes'=>449,'finished'=>true),
'5_misto_1_kolo'=>array('name'=>'EP - 5. místo<br/>1. kolo','file'=>'cf_24_2018_volba_5__místa_kandidátky_do_EP_1__kolo.txt','votes'=>375,'finished'=>true),
'5_misto_II_1_kolo'=>array('name'=>'EP - 5. místo<br/>nová volba - 1. kolo','file'=>'https://helios.pirati.cz/helios/elections/2b166e4f-ce02-4337-ad2b-a2fe0aaf7fbb/voters/list?page=1&limit=1000&q=','votes'=>0, 'finished'=>false),
);

foreach(file('./data/ks_jihocesky_kraj.txt') as $line) {
  $clenove[]=trim($line);
  }

$voldata=array();

foreach ($helios as $hindex=>$hdata) {
  if (substr($hdata['file'],0,5)!='https') {
    $lines=file("./data/".$hdata['file']);
    foreach ($lines as $line) {
      $ee=explode("\t",$line);
      if (count($ee)==2) {
        $id=substr($ee[0],9); //." :: ".$ee[1]."<br/>");
        if (in_array($id,$clenove)) $voldata[$id][$hindex]=$ee[1]; //(strlen($ee[1]>1)?1:0);
        }
      }
    } else {
    $soub="./data/".substr($hdata['file'],42,36).".txt";
    
    $rcount=0;
    $count=0;
    $count2=0;
    if (file_exists($soub)) {
      $now=time();
      $soubor_time=filemtime($soub);
      $rozdil=$now-$soubor_time;
      if ($rozdil<$timelimit) {
        if (($handle = fopen($soub, "r")) !== FALSE) {
          while (!flock($handle, LOCK_SH)) { usleep(1);}          
          while (!feof($handle)) {
            $line = fgets($handle);
            $ee=explode("\t",$line);
            if (count($ee)==2) {
              $id=substr($ee[0],9); //." :: ".$ee[1]."<br/>");
              $count++;
              if (strlen($ee[1])>12) $count2++;
              if (in_array($id,$clenove)) {
                $rcount++;
                $voldata[$id][$hindex]=$ee[1]; //(strlen($ee[1]>1)?1:0);
                }
              }
            }
          flock($handle, LOCK_UN);
          fclose($handle);
          $helios[$hindex]['voters']=$count;  
          $helios[$hindex]['votes']=$count2;  
          $helios[$hindex]['update']=$soubor_time;  
          }
        }
      }
    if ($rcount==0) {
      $now=time();      
      $handle = fopen($soub, 'w');
      while (!flock($handle, LOCK_EX)) { usleep(1);}
      ftruncate($handle, 0);
      $count=0; $count2=0;    
      $html = file_get_html($hdata['file']);
      foreach($html->find("td") as $element) {
        $count++;
        if ($count%2==1) {
          $id=$element->plaintext;
          } else {
          $vote=$element->plaintext;
          fwrite($handle, "pirateid ".$id."\t".$vote."\n");
          if (in_array($id,$clenove)) $voldata[$id][$hindex]=$vote;
          if (strlen($vote)>12) $count2++;
          }
        }
      $helios[$hindex]['voters']=floor($count/2);  
      $helios[$hindex]['votes']=$count2;  
      $helios[$hindex]['update']=$now;  
      //$b3=explode(" ",$html->find("b",2)->plaintext);
      //$celk=$html->find('div#contentbody')->outertext;  
      //fwrite($handle, "votes ".$b3[0]."\t".$celk);   
      fflush($handle);
      flock($handle, LOCK_UN);
      fclose($handle);
      }                 
    }
  }  
  
?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Účast na hlasování CF - KS Jihočeský kraj</title>
  <link href='https://fonts.googleapis.com/css?family=Roboto&amp;subset=latin-ext' rel='stylesheet'>
  <link href='./normalize.css' rel='stylesheet'>
  <style>
  body {font-family: 'Roboto', sans-serif; margin:1em;}
  tr.gr {background-color:rgb(230, 255, 230);}
  tr.rd {background-color:rgb(255, 230, 230);}
  </style>
</head>
<body>
<h1>Účast na posledních hlasování CF</h1>
<h2>Jihočeský kraj</h2>
<? 
$out="<table style='border-collapse: collapse;'>";
$out.="<tr style='font-weight:bold;'><td style='border:1px solid #d0d0d0; padding:0.3em;'>Jméno</td>";
foreach ($helios as $helid=>$heldata) {
  $out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center;'>".$heldata['name']."</td>";
  }
$out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:right;'>Celkem</td></tr>";  

$poc_voleb=count($helios);
foreach ($voldata as $iid=>$iddata) {
  $out2="<tr class='###'><td style='border:1px solid #d0d0d0; padding:0.3em;'>".$iid."</td>";
  $cpocet=0;
  foreach ($iddata as $hid=>$vote) {
    if (strlen($vote)>12) {
      $vot[$hid]++;
      $vt="&#9989;";
      $cpocet++;
      } else $vt="&#10060;";
    $out2.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center; font-size:125%;'>".$vt."</td>";
    }
  $out2.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:right;'>".$cpocet." (".number_format($cpocet/count($helios)*100,0)."%)</td></tr>";
  if ($cpocet==$poc_voleb) $out2=str_replace('###','gr',$out2); else if ($cpocet==0) $out2=str_replace('###','rd',$out2);
  $out.=$out2;  
  }
  


$out.="<tr style='font-weight:bold;'><td style='border:1px solid #d0d0d0; padding:0.3em;'>Účast krajsky</td>";
foreach ($helios as $helid=>$heldata) {
  $out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center;'>".$vot[$helid]." (".number_format($vot[$helid]/count($voldata)*100,2)."%)</td>";
  }
$out.="</tr>";  

$out.="<tr style='font-weight:bold;'><td style='border:1px solid #d0d0d0; padding:0.3em;'>Účast celorepublikově</td>";
foreach ($helios as $helid=>$heldata) {
  if (array_key_exists('voters',$heldata)) $total=$heldata['voters']; else $total=$clenu_total;
  $out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center;'>".$heldata['votes']." (".number_format($heldata['votes']/$total*100,2)."%)</td>";
  }
$out.="</tr>";  

$out.="</table>";

foreach ($helios as $helid=>$heldata) {
  if ($heldata['finished']==false) $out.="<p>Poslední aktualizace (".str_replace("<br/>","/",$heldata['name'])."): ".date("Y-m-d H:i",$heldata['update'])."<br/>";
  }

echo $out;
//echo "<pre>".print_r($voldata,true)."</pre>";
?>
</body>
</html>
    