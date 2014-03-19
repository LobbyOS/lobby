<?
function makeDatabase($pref, $db){
 try {
  /* Create Tables */
  $sql=$db->prepare("
   CREATE TABLE IF NOT EXISTS `{$pref}options` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(64) NOT NULL,
    `val` tinytext NOT NULL,
    PRIMARY KEY (`id`)
   ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;
   CREATE TABLE IF NOT EXISTS `{$pref}data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `app` varchar(50) NOT NULL,
    `name` varchar(150) NOT NULL,
    `content` longtext NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
   ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"
  );
  $sql->execute();
  /* Insert The Default Data In To Tables */
  $Linfo=file_get_contents(L_ROOT."lobby.json");
  $Linfo=json_decode($Linfo, true);
  $sql=$db->prepare("
   INSERT INTO `{$pref}options` (
    `id`, 
    `name`, 
    `val`
    ) VALUES (
     NULL, 
     'active_apps', 
     '[\"ledit\"]'
    ),(
     NULL,
     'lobby_version',
     ?
    ),(
     NULL,
     'lobby_version_release',
     ?
    );"
  );
  $sql->execute(array($Linfo['version'], $Linfo['released']));
  return true;
 }catch(PDOException $Exception){
  return false;
 }
}
?>
