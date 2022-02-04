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
      'version'=>"ezer{$_SESSION[$ezer_root]['ezer']}",
      'options'=>(object)array()
  );

  // informace pro debugger o poloze ezer modulů
  $dbg_info= (object)array(
    'src_path'  => array('test',$ezer_version) // poloha a preference zdrojových modulů
    );

  // specifické PHP moduly
  $app_php=   array(
    );
  
  // aplikace se startem v podsložce
  require_once("$ezer_version/ezer_ajax.php");
  
/** ===========================================================================================> GIT */
# ----------------------------------------------------------------------------------------- git make
# provede git par.cmd>.git.log a zobrazí jej
# fetch pro lokální tj. vývojový server nepovolujeme
function git_make($par) {
  global $abs_root, $ezer_version;
  $bean= preg_match('/bean/',$_SERVER['SERVER_NAME'])?1:0;
                                                    display("bean=$bean, $ezer_version");
  $cmd= $par->cmd;
  $folder= $par->folder;
  $lines= '';
  $msg= "";
  // proveď operaci
  switch ($par->op) {
    case 'show':
      $msg.= file_get_contents("$abs_root/docs/.git.log");
      break;
    case 'cmd':
      // nastav složku pro Git
      if ( $folder=='ezer') 
        chdir("../$ezer_version");
      elseif ( $folder=='skins') 
        chdir("./skins");
      // zruš starý obsah .git.log
      $f= @fopen("$abs_root/docs/.git.log", "r+");
      if ($f !== false) {
          ftruncate($f, 0);
          fclose($f);
      }
      // proveď příkaz Git
      $state= 0;
      $branch= $folder=='ezer' ? $ezer_version : 'master';
      switch ($cmd) {
        case 'log':
        case 'status':
          $exec= "git $cmd>$abs_root/docs/.git.log";
          exec($exec,$lines,$state);
          $msg.= "$state:$exec\n";
          break;
        case 'pull':
          $exec= "git pull origin $branch>$abs_root/docs/.git.log";
          exec($exec,$lines,$state);
          $msg.= "$state:$exec\n";
          break;
        case 'fetch':
          if ( $bean) 
            $msg= "na vývojových serverech (*.bean) příkaz fetch není povolen ";
          else {
            $exec= "git pull origin $branch>$abs_root/docs/.git.log";
            exec($exec,$lines,$state);
            $msg.= "$state:$exec\n";
            $exec= "git reset --hard origin/$branch>$abs_root/docs/.git.log";
            exec($exec,$lines,$state);
            $msg.= "$state:$exec\n";
          }
          break;
      }
      if ( $folder=='ezer'||$folder=='skins') 
        chdir($abs_root);
      break;
  }
  $msg= nl2br(htmlentities($msg));
  $msg= "<i>Synology: musí být spuštěný Git Server (po aktualizaci se vypíná)</i><hr>$msg";
  return $msg;
}

