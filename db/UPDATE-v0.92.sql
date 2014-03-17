/* Update from v 0.91 to 0.92 **/
UPDATE `settings` set `version` = '0.92'; 	/* UPDATE version */
/* create table changelog */
CREATE TABLE `changelog` (
  `cid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype` set('ip_addr','subnet','section') NOT NULL DEFAULT '',
  `coid` int(11) unsigned NOT NULL,
  `cuser` int(11) unsigned NOT NULL,
  `caction` set('add','edit','delete','truncate','resize','perm_change') NOT NULL DEFAULT 'edit',
  `cresult` set('error','success') NOT NULL DEFAULT '',
  `cdate` datetime NOT NULL,
  `cdiff` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`cid`),
  KEY `coid` (`coid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* changelog switch */
ALTER TABLE `settings` ADD `enableChangelog` TINYINT(1)  NOT NULL  DEFAULT '1';
