<?php # (c) 2022 Martin Smidek <martin@smidek.eu>

  global // import 
    $ezer_root, $ezer_version; 
  global // export
    $EZER, $ezer_server;
  
  // vyzvednutí ostatních hodnot ze SESSION
  $ezer_server=  $_SESSION[$ezer_root]['ezer_server'];
  $ezer_version= "ezer{$_SESSION[$ezer_root]['ezer']}";
  $abs_root=     $_SESSION[$ezer_root]['abs_root'];
  $rel_root=     $_SESSION[$ezer_root]['rel_root'];
  chdir($abs_root);

  // klíče
  $deep_root= "../files/test";
  require_once("$deep_root/ipad.dbs.php");
  
  // inicializace objektu Ezer
  $EZER= (object)array(
      'version'=>$ezer_version,
      'options'=>(object)array()
  );

  // informace pro debugger o poloze ezer modulů
  $dbg_info= (object)array(
    'src_path'  => array('test',$ezer_version) // poloha a preference zdrojových modulů
    );

  // specifické PHP moduly
  $app_php=   array("$ezer_version/server/comp2.php");
  
  // aplikace se startem v podsložce
  require_once("$ezer_version/ezer_ajax.php");
  
/** ===========================================================================================> GIT */
# ----------------------------------------------------------------------------------------- git make
# provede git par.cmd
# fetch pro lokální tj. vývojový server nepovolujeme
function git_make($par) {
  global $abs_root, $ezer_version;
  $bean= preg_match('/bean/',$_SERVER['SERVER_NAME'])?1:0;
  display("$ezer_version, abs_root=$abs_root, bean=$bean");
  if ($ezer_version!='ezer3.2') { fce_error("POZOR není aktivní jádro 3.2 ale $ezer_version"); }
  $cmd= $par->cmd;
  $folder= $par->folder;
  $lines= array();
  $msg= "";
  // nastav složku pro Git
  if ( $folder=='ezer') 
    chdir("./$ezer_version");
  elseif ( $folder=='skins') 
    chdir("./skins");
  elseif ( $folder=='.') 
    chdir(".");
  else
    fce_error('chybná aktuální složka');
  // proveď příkaz Git
  $state= 0;
  $branch= $folder=='ezer' ? $ezer_version : 'master';
  switch ($cmd) {
    case 'log':
    case 'status':
      $exec= "git $cmd";
      display($exec);
      exec($exec,$lines,$state);
      $msg.= "$state:$exec\n";
      break;
    case 'pull':
      $exec= "git pull origin $branch";
      display($exec);
      exec($exec,$lines,$state);
      $msg.= "$state:$exec\n";
      break;
    case 'fetch':
      if ( $bean) 
        $msg= "na vývojových serverech (*.bean) příkaz fetch není povolen ";
      else {
        $exec= "git pull origin $branch";
        display($exec);
        exec($exec,$lines,$state);
        $msg.= "$state:$exec\n";
        $exec= "git reset --hard origin/$branch";
        display($exec);
        exec($exec,$lines,$state);
        $msg.= "$state:$exec\n";
      }
      break;
  }
  // případně se vrať na abs-root
  if ( $folder=='ezer'||$folder=='skins') 
    chdir($abs_root);
  // zformátuj výstup
  $msg= nl2br(htmlentities($msg));
  $msg= "<i>Synology: musí být spuštěný Git Server (po aktualizaci se vypíná)</i><hr>$msg";
  $msg.= $lines ? '<hr>'.implode('<br>',$lines) : '';
  return $msg;
}
# ---------------------------------------------------------------------------------------- test_auto
# test autocomplete
function test_auto($patt,$par) {  trace();
                                                      debug($par,"test_auto.par");
  $a= (object)array();
  $limit= 10;
  $n= 0;
  if ( !$patt ) {
    $a->{0}= "... zadejte vzor";
  }
  else {
    if ( $par->prefix ) {
      $patt= "{$par->prefix}$patt";
    }
    // zpracování vzoru
    $qry= "SELECT id_jmena AS _key,jmeno AS _value
           FROM _jmena
           WHERE jmeno LIKE '$patt%' ORDER BY jmeno LIMIT $limit";
                                                        display("test_auto:$qry");
    $res= pdo_qry($qry);
    while ( $res && $t= pdo_fetch_object($res) ) {
      if ( ++$n==$limit ) break;
      $a->{$t->_key}= $t->_value;
    }
                                                        display("test_auto:$n,$limit");
    // obecné položky
    if ( !$n )
      $a->{0}= "... nic nezačíná $patt";
    elseif ( $n==$limit )
      $a->{999999}= "... a další";
  }
                                                      debug($a,"test_auto");
  return $a;
}
