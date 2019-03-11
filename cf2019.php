<?
$time['start']=microtime(true);  
header('Content-type: text/html; charset=utf-8');
ob_start();
include_once('simple_html_dom.php'); 

$verbose=false;
$clenu_total=892;
$timelimit_cache=60;
$timelimit=3600;
$timelimit_users=3600*48;

$clenove=array();

$url_prefix="https://helios.pirati.cz/helios/elections/";
$url_postfix="/voters/list?page=1&limit=1000&q=";
$url_test="/voters/list?page=1&limit=1&q=";
$forum_url="https://forum.pirati.cz/memberlist.php?mode=group&g=";

$helios=array(
'lidr_1_kolo'=>array('name'=>'EP - Lídr kandidátky<br/>1. kolo',              'type'=>'helios', 'file'=>'fb585256-1bb1-11e9-b0a7-00000a2a0114', 'finished'=>true),
'lidr_2_kolo'=>array('name'=>'EP - Lídr kandidátky<br/>2. kolo',              'type'=>'helios', 'file'=>'f23ad74f-81c8-483d-8df2-4cccd46a46ed', 'finished'=>true),
'celo_1_kolo'=>array('name'=>'EP - Čelo kandidátky<br/>1. kolo',              'type'=>'helios', 'file'=>'ca07d3d8-3eba-4edc-b7af-a053a088f571', 'finished'=>true),
'celo_2_kolo'=>array('name'=>'EP - Čelo kandidátky<br/>2. kolo',              'type'=>'helios', 'file'=>'33819892-5675-4edf-83af-09c06ae6a5af', 'finished'=>true),
'5_misto_1_kolo'=>array('name'=>'EP - 5. místo<br/>1. kolo',                  'type'=>'helios', 'file'=>'9ae5e2d4-10bb-4d1b-95ab-133ffd230322', 'finished'=>true),
'5_misto_II_1_kolo'=>array('name'=>'EP - 5. místo<br/>nová volba - 1. kolo',  'type'=>'helios', 'file'=>'2b166e4f-ce02-4337-ad2b-a2fe0aaf7fbb', 'finished'=>true),
'5_misto_II_2_kolo'=>array('name'=>'EP - 5. místo<br/>nová volba - 2. kolo',  'type'=>'helios', 'file'=>'5754903c-15e3-4ea4-9adb-cd6545cf29c1', 'finished'=>true),
'zbytek_ep_1_kolo'=>array('name'=>'Volba zbylé části<br/>kandidátky do EP - 1. kolo',  'type'=>'helios', 'file'=>'776f3d3d-1224-4030-938d-1936539cbaeb', 'finished'=>true),
'zbytek_ep_2_kolo'=>array('name'=>'Volba zbylé části<br/>kandidátky do EP - 2. kolo',  'type'=>'helios', 'file'=>'67f6eccd-bdca-4887-a787-898f3e3493bb', 'finished'=>true),
'ep14-28_1_kolo'=>array('name'=>'Volba 14.-28. místa<br/>kandidátky do EP - 1. kolo',  'type'=>'helios', 'file'=>'b36518ad-2408-4ba7-b017-569bc77b1c20', 'finished'=>true),
'ep14-16_2_kolo'=>array('name'=>'Volba 14.-16. místa<br/>kandidátky do EP - 2. kolo',  'type'=>'helios', 'file'=>'9fab4316-36c1-11e9-8ebd-00000a2a0114', 'finished'=>true),
'ep17-28_1_kolo'=>array('name'=>'Volba 17.-28. místa<br/>kandidátky do EP - 1. kolo',  'type'=>'helios', 'file'=>'3c34e786-d346-442a-81d1-4a7cc3ebfc71', 'finished'=>true),
'ep17-28_2_kolo'=>array('name'=>'Volba 17.-28. místa<br/>kandidátky do EP - 2. kolo',  'type'=>'helios', 'file'=>'76a38f1e-fc8f-4dd9-b3f3-8f05cb90ee56', 'finished'=>true),
'02-2019_dopln_rv_1_kolo'=>array('name'=>'CF 2/2019 Doplňující volba<br/>RV - 1. kolo',  'type'=>'helios', 'file'=>'6a5f69fe-3bde-11e9-ac9d-00000a2a0114', 'finished'=>true),
);

$clendb=array(
'liberecky'=>array('name'=>'Liberecký kraj', 'file'=>'./data/ks_liberecky_kraj.txt', 'forum_gid'=>41),
'jihocesky'=>array('name'=>'Jihočeský kraj', 'file'=>'./data/ks_jihocesky_kraj.txt', 'forum_gid'=>40),
'jihomoravsky'=>array('name'=>'Jihomoravský kraj', 'file'=>'./data/ks_jihomoravsky_kraj.txt', 'forum_gid'=>36),
'karlovarsky'=>array('name'=>'Karlovarský kraj', 'file'=>'./data/ks_karlovarsky_kraj.txt', 'forum_gid'=>43),
'kralovehradecky'=>array('name'=>'Královéhradecký kraj', 'file'=>'./data/ks_kralovehradecky_kraj.txt', 'forum_gid'=>32),
'moravskoslezsky'=>array('name'=>'Moravskoslezský kraj', 'file'=>'./data/ks_moravskoslezsky_kraj.txt', 'forum_gid'=>34),
'olomoucky'=>array('name'=>'Olomoucký kraj', 'file'=>'./data/ks_olomoucky_kraj.txt', 'forum_gid'=>38),
'pardubicky'=>array('name'=>'Pardubický kraj', 'file'=>'./data/ks_pardubicky_kraj.txt', 'forum_gid'=>35),
'plzensky'=>array('name'=>'Plzeňský kraj', 'file'=>'./data/ks_plzensky_kraj.txt', 'forum_gid'=>44),
'praha'=>array('name'=>'Praha', 'file'=>'./data/ks_praha.txt', 'forum_gid'=>33),
'praha-hostujici'=>array('name'=>'Praha - hostující', 'file'=>'./data/ks_praha_hostujici.txt', 'forum_gid'=>416),
'stredocesky'=>array('name'=>'Středočeský kraj', 'file'=>'./data/ks_stredocesky_kraj.txt', 'forum_gid'=>78),
'ustecky'=>array('name'=>'Ústecký kraj', 'file'=>'./data/ks_ustecky_kraj.txt', 'forum_gid'=>42),
'vysocina'=>array('name'=>'Kraj Vysočina', 'file'=>'./data/ks_kraj_vysocina.txt', 'forum_gid'=>37),
'zlinsky'=>array('name'=>'Zlínský kraj', 'file'=>'./data/ks_zlinsky_kraj.txt', 'forum_gid'=>39),
'predsedove'=>array('name'=>'Krajští předsedové', 'file'=>'./data/predsedove.txt', 'forum_gid'=>45),
'poslanci'=>array('name'=>'Poslanci', 'file'=>'./data/poslanci.txt', 'forum_gid'=>474),
'republikovy_vybor'=>array('name'=>'Republikový výbor', 'file'=>'./data/republikovy_vybor.txt', 'forum_gid'=>404),
);

if (($_GET["kraj"]!="") && (array_key_exists($_GET["kraj"],$clendb))) $kraj=$_GET["kraj"]; else $kraj='liberecky';

$time['init']=microtime(true);  

// handling nacteni a lokalniho cachingu clenu KS

$clenove=array();
$now=time();
$soub=$clendb[$kraj]['file'];
if (file_exists($soub)) {
  $soubor_time=filemtime($soub);
  $rozdil=$now-$soubor_time;
  if ($rozdil<$timelimit_users) {
    if (($handle = fopen($soub, "r")) !== FALSE) {
      while (!flock($handle, LOCK_SH)) {usleep(1);}     
      while (!feof($handle)) {
        $cldata=explode("\t",trim(fgets($handle)));
        $clenove[] = $cldata[0];
        if (count($cldata)==3) if ($cldata[2]!="Test") $clenove_data[$cldata[0]]=json_decode($cldata[2],true);
        }
      flock($handle, LOCK_UN);
      fclose($handle);
      }
    }  
  }  

$time['clenove_load']=microtime(true);  

if (count($clenove)==0) {
  
  echo "<div class='load'><img src='806.svg' style='height:1em;'/>&nbsp;Aktualizuji databázi členů skupiny (".$clendb[$kraj]['name'].") ...</div>";
  ob_flush();flush();
  $search=true;
  $iter=0;
  $clenove=array();
  $ids="";
  while ($search) {
    $html = file_get_html($forum_url.$clendb[$kraj]['forum_gid']."&start=".$iter*100);
    foreach($html->find("a.username,a.username-coloured") as $index=>$element) {
      $username=$element->innertext;
      $clenove[]=$username;
      $url=$element->href;
      $id=explode("&amp;",explode("amp;u=",$url)[1])[0];
      $ids.=(($ids!="")?",":"").$id;
      $clenove_data[$username]=array('id'=>$id,'username'=>$username);
      }
    if (count($html->find("i.fa-chevron-right"))==1) $search=true; else $search=false;
    $iter++;  
    }
  $json=file_get_contents("https://piratiliberec.mfnet.cz/forumapi/api.php?id=".$ids);
  $json_data=json_decode($json,true);  
  $handle = fopen($soub, 'w');
  while (!flock($handle, LOCK_EX)) {usleep(1);}
  ftruncate($handle, 0);
  foreach($clenove as $cli=>$cld) {
    $id=$clenove_data[$cld]['id'];
    fwrite($handle,$cld."\t".$id."\t".json_encode($json_data[$id])."\n");
    $clenove_data[$cld]=$json_data[$id];
    }
  fflush($handle);
  flock($handle, LOCK_UN);
  fclose($handle);
  $time['clenove_get']=microtime(true);  
  }


// handling nacteni a lokalniho cachingu vysledku jednotlivych voleb

$voldata=array();

foreach ($helios as $hindex=>$hdata) {
  if ($hdata['type']=='local') {
    $lines=file("./data/".$hdata['file']);
    foreach ($lines as $line) {
      $ee=explode("\t",$line);
      if (count($ee)==2) {
        $id=substr($ee[0],9); //." :: ".$ee[1]."<br/>");
        if (in_array($id,$clenove)) $voldata[$id][$hindex]=$ee[1]; //(strlen($ee[1]>1)?1:0);
        }
      }
    } else {
    $soub="./data/".$hdata['file'].".txt";

    $helios[$hindex]['voters']=0;  
    $helios[$hindex]['votes']=0;  
    $helios[$hindex]['update']=0;  
    
    $rcount=0;
    $count=0;
    $count2=0;
    if (file_exists($soub)) {
      $now=time();
      $soubor_time=filemtime($soub);
      $rozdil=$now-$soubor_time;
      if (($rozdil<$timelimit) || ($hdata['finished'])) {
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
    
    if (($rcount==0) || ($rozdil>$timelimit_cache)) {  
      if (($rcount!=0) && ($hdata['finished']==false)) {  
        $html = file_get_html($url_prefix.$hdata['file'].$url_test);
        $new_pocet=explode(" ",$html->find("div.row b",2)->plaintext)[0];
        if ($new_pocet==$helios[$hindex]['votes']) touch($soub);
        } elseif ($hdata['finished']==true) {
        if ($rcount==0) $new_pocet=-1; else $new_pocet=$helios[$hindex]['votes'];
        } else $new_pocet=-1;
      //echo $new_pocet." ".$helios[$hindex]['votes'];  
  
      //if ($rcount==0) {
      if ($new_pocet!=$helios[$hindex]['votes']) {
        echo "<div class='load'><img src='806.svg' style='height:1em;'/>&nbsp;Aktualizuji data volby helios (".$hindex.(($new_pocet>0)?(" - ".$new_pocet." hlasů"):"").") ...</div>";
        ob_flush();flush();
        $now=time();      
        $handle = fopen($soub, 'w');
        while (!flock($handle, LOCK_EX)) { usleep(1);}
        ftruncate($handle, 0);
        $count=0; $count2=0;    
        $html = file_get_html($url_prefix.$hdata['file'].$url_postfix);
        foreach($html->find("table.pretty td") as $element) {
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
        $time['parsex_'.$hindex]=microtime(true);
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
  }  

$time['volby_load']=microtime(true);  


  
?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Účast na hlasování CF - <?;echo $clendb[$kraj]['name'];?></title>
  <link href='https://fonts.googleapis.com/css?family=Roboto&amp;subset=latin-ext' rel='stylesheet'>
  <link href='./normalize.css' rel='stylesheet'>
  <style>
  body {font-family: 'Roboto', sans-serif; margin:1em;}
  div.load {display:none;}
  tr.gr {background-color:rgb(230, 255, 230);}
  tr.rd {background-color:rgb(255, 230, 230);}
  .u-mirror-horizontally {display: inline-block;transform: matrix(-1, 0, 0, 1, 0, 0) !important;}
  </style>
</head>
<body>
<h1>Účast na posledních hlasování CF</h1>
<h2><?;echo $clendb[$kraj]['name'];?></h2>
<p>
<?
foreach ($clendb as $clid=>$cldata) {
  if ($kraj!=$clid) {
    echo "<a href='./cf2019.php?kraj=".$clid."'>".$cldata['name']."</a>&nbsp;";
    } else {
    echo $cldata['name']."&nbsp;";
    }
  }
?>
</p>
<? 
$out="<table style='border-collapse: collapse;'>";
$out.="<tr style='font-weight:bold;'><td style='border:1px solid #d0d0d0; padding:0.3em;'>Jméno</td>";
foreach ($helios as $helid=>$heldata) {
  $out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center;'><a href='".$url_prefix.(($heldata['type']=="local")?$heldata['url']:$heldata['file'])."/view' target='".$heldata['name']."'>".$heldata['name']."</a></td>";
  }
$out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:right;'>Celkem</td></tr>";  

//$poc_voleb=count($helios);
foreach ($voldata as $iid=>$iddata) {
  if (isset($clenove_data[$iid]['name'])) {
    $name=$clenove_data[$iid]['name'];
    if (strlen($name)<6) $name.="&nbsp;(".$iid.")";
    } else $name=$iid;
  if (isset($clenove_data[$iid]['imgsrc'])) $name="<img src='".$clenove_data[$iid]['imgsrc']."' style='height:1.4rem;'>&nbsp;".$name;
  if (isset($clenove_data[$iid]['gender'])) {
    if ($clenove_data[$iid]['gender']=="F") $name.="&nbsp;&#x2640;"; elseif ($clenove_data[$iid]['gender']=="M") $name.="&nbsp;&#x2642;";
    } 
  if (isset($clenove_data[$iid]['age'])) $name.="&nbsp;(".$clenove_data[$iid]['age'].")";
  $out2="<tr class='###'><td style='border:1px solid #d0d0d0; padding:0.3em;'>".$name."</td>";
  $cpocet=0;
  $poc_voleb=0;
  foreach ($helios as $helid=>$heldata) {
    if (array_key_exists($helid,$iddata)) {
      $pocvoters[$helid]++;
      $hid=$helid; $vote=$iddata[$hid];
      $poc_voleb++;
  //foreach ($iddata as $hid=>$vote) {
    if (strlen($vote)>12) {
      $vot[$hid]++;
      $vt="&#9989;";
      $cpocet++;
      } else $vt="&#10060;";
    } else $vt="&#10134;";  
    $out2.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center; font-size:125%;'>".$vt."</td>";
    //}
    }  
  $out2.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:right;'>".$cpocet." (".number_format($cpocet/$poc_voleb*100,0)."%)</td></tr>";
  if ($cpocet==$poc_voleb) $out2=str_replace('###','gr',$out2); else if ($cpocet==0) $out2=str_replace('###','rd',$out2);
  $out.=$out2;  
  }
  


$out.="<tr style='font-weight:bold;'><td style='border:1px solid #d0d0d0; padding:0.3em;'>Účast</td>";
foreach ($helios as $helid=>$heldata) {
  //$out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center;'>".$vot[$helid]." (".number_format($vot[$helid]/count($voldata)*100,2)."%)</td>";
  $out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center;'>".$vot[$helid]." (".number_format($vot[$helid]/$pocvoters[$helid]*100,2)."%)</td>";
  }
$out.="</tr>";  

$out.="<tr style='font-weight:bold;'><td style='border:1px solid #d0d0d0; padding:0.3em;'>Účast celorepublikově</td>";
foreach ($helios as $helid=>$heldata) {
  if (array_key_exists('voters',$heldata)) $total=$heldata['voters']; else $total=$clenu_total;
  $out.="<td style='border:1px solid #d0d0d0; padding:0.3em; text-align:center;'>".$heldata['votes']." (".number_format($heldata['votes']/$total*100,2)."%)</td>";
  }
$out.="</tr>";  

$out.="</table>";

$time['end']=microtime(true);

$last=0;
$timing=number_format($time['end']-$time['start'],3,'.',' ')."s";
foreach ($time as $index=>$timestamp) {
  $rozdil=$timestamp-$last;
  if ($last!=0) $timing.=" - ".$index." [".number_format($rozdil,3,'.',' ')."s]";
  $last=$timestamp; 
  }



$out.="<p>&#9989;&nbsp;volil(a)&nbsp; &nbsp;&#10060;&nbsp;nevolil(a)&nbsp; &nbsp;&#10134;&nbsp;nebyl(a)&nbsp;na&nbsp;seznamu&nbsp; <span class='u-mirror-horizontally'>©</span> 2019 Marek Förster - Piráti, marek.forster@pirati.cz, <a href='https://github.com/madbeyk/pirati-helios-voting' target='_blank'>github</a>".(($verbose)?"<br/>".$timing:"")."</p>";

foreach ($helios as $helid=>$heldata) {
  if ($heldata['finished']==false) $out.="<p>Poslední aktualizace (".str_replace("<br/>","/",$heldata['name'])."): ".date("Y-m-d H:i",$heldata['update'])."<br/>";
  }

echo $out;
//echo "<pre>".print_r($voldata,true)."</pre>";
?>
</body>
</html>
<?;ob_end_flush();