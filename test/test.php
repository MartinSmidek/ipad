<?php
  // detekce aktivního serveru
  $ezer_server= 
    $_SERVER["SERVER_NAME"]=='ipad.bean'        ? 0 : (        	// 0:lokální 
    $_SERVER["SERVER_NAME"]=='ipad.setkani.org' ? 1 : -1);   	// 1:synology YMCA

  // verze použitého jádra Ezeru
  $ezer_version= "ezer3.2"; 
  $_GET['editor']= 0;
  $_GET['dbg']= 0; 

  // parametry aplikace LAB
  $app_name=  "iPad ";
  $app_root=  'test';
  $app_js=    array();
  $app_css=   array();
  $skin=      'ck';

  $abs_roots= array(
      "C:/Ezer/beans/ipad",
      "/volume1/web/www/ipad"
    );
  $rel_roots= array(
      "http://ipad.bean:8080",
      "https://ipad.setkani.org"
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
  
  // je to aplikace se startem v podsložce
  require_once("../$ezer_version/ezer_main.php");

  ?>
