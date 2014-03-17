/* Update from v 0.96 to 0.97 **/
UPDATE `settings` set `version` = '0.97'; 	/* UPDATE version */
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* remove HTML mail option from settings */
ALTER TABLE `settings` DROP `htmlMail`;
/* add mailSettings table */
CREATE TABLE `settingsMail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mtype` set('localhost','smtp') NOT NULL DEFAULT 'localhost',
  `mauth` set('yes','no') NOT NULL DEFAULT 'no',
  `mserver` varchar(128) DEFAULT NULL,
  `mport` int(5) DEFAULT '25',
  `muser` varchar(64) DEFAULT NULL,
  `mpass` varchar(64) DEFAULT NULL,
  `mAdminName` varchar(64) DEFAULT NULL,
  `mAdminMail` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
INSERT INTO `settingsMail` (`id`, `mtype`, `mauth`) VALUES (1, 'localhost', 'no');