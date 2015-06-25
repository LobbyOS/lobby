<?php
if(getOption("admin_installed") == null && \Lobby::$installed){
  /**
   * Install Module
   */
  $salt = \Lobby::randStr(15);
  $cookie = \Lobby::randStr(15);
  saveOption("admin_secure_salt", $salt);
  saveOption("admin_secure_cookie", $cookie);
  
  $prefix = \Lobby\DB::$prefix;
  /**
   * Create `users` TABLE
   */
  $sql = \Lobby\DB::$dbh->prepare("CREATE TABLE IF NOT EXISTS `{$prefix}users` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `username` varchar(10) NOT NULL,
	  `email` tinytext NOT NULL,
	  `password` varchar(64) NOT NULL,
	  `password_salt` varchar(20) NOT NULL,
	  `name` varchar(30) NOT NULL,
	  `created` datetime NOT NULL,
	  `attempt` varchar(15) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
  if($sql->execute() != 0){
    saveOption("admin_installed", "true");
  }
}
