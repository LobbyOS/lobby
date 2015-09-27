/* Candidates */

CREATE TABLE IF NOT EXISTS `electionCandidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidateName` text NOT NULL,
  `gender` varchar(6) NOT NULL,
  `votes` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

/* Voters */

CREATE TABLE IF NOT EXISTS `electionVoters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voterID` varchar(10) NOT NULL,
  `voted` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
