<?php
  // detekce aktivního serveru
  $ezer_server= 
    $_SERVER["SERVER_NAME"]=='ipad.bean'      ? 0 : (       // 0:lokální 
    $_SERVER["SERVER_NAME"]=='ezer.smidek.eu' ? 1 : -1);   	// 1:synology YMCA

  // verze použitého jádra Ezeru
  $ezer_version= isset($_GET['ezer']) ? "ezer{$_GET['ezer']}" : 'ezer3.2'; 

  // defaultní nastavení přepínačů
  $_GET['pdo']= 2;
  $_GET['editor']= 0;
  $_GET['dbg']= 1; 
  $_GET['err']= 1; 
  $_GET['gmap']= 0; 
  $_GET['smap']= 0; 
//  $_GET['touch']= 1; 
//  $_GET['menu']= "touch.brow.m.g.i";
  
  // parametry aplikace LAB
  $app_name=  "iPad";
  $app_root=  'test';
  $app_js=    array();
  $app_css=   array("$ezer_version/client/wiki.css");
  $skin=      'ck';

  $abs_roots= array(
      "C:/Ezer/beans/ipad",
      "/volume1/web/www/ipad"
    );
  $rel_roots= array(
      "http://ipad.bean:8080",
      "http://ezer.smidek.eu"
    );

  // (re)definice Ezer.options
  $favicons= array('test_local.png','test.png');
  $add_pars= array(
    'favicon' => $favicons[$ezer_server]
  );
  $add_options= (object)array(
    'watch_git'    => 1,                // sleduj git-verzi aplikace a jádra, při změně navrhni restart
    'curr_version' => 1,                // při přihlášení je nahrazeno nejvyšší ezer_kernel.version
    'curr_users'   => 1                 // zobrazovat v aktuální hodině aktivní uživatele
  );
  $app_login= 'Guest/';                   // pouze pro automatické přihlášení
  
  // bude vloženo do objektu Ezer.konst
  $const= (object)array(
    'aaa' => "5"
  , 'll' => 260
  );
  // je to aplikace se startem v podsložce
  require_once("../$ezer_version/ezer_main.php");

  ?>
