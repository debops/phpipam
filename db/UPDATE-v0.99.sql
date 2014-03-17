/* Update from v 0.98 to 0.99 **/
UPDATE `settings` set `version` = '0.99'; 	/* UPDATE version */
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* clear verification */
UPDATE `settings` set `dbverified` = '0';
/* set db verified flag */
ALTER TABLE `settings` ADD `vcheckDate` DATETIME  NULL  AFTER `editDate`;
/* rename switches to devices */
RENAME TABLE `switches` TO `devices`;
/* add devicetypes to database */
CREATE TABLE `deviceTypes` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tname` varchar(128) DEFAULT NULL,
  `tdescription` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `deviceTypes` (`tid`, `tname`, `tdescription`)
VALUES
	(1, 'Switch', 'Switch'),
	(2, 'Router', 'Router'),
	(3, 'Firewall', 'Firewall'),
	(4, 'Hub', 'Hub'),
	(5, 'Wireless', 'Wireless'),
	(6, 'Database', 'Database'),
	(7, 'Workstation', 'Workstation'),
	(8, 'Laptop', 'Laptop'),
	(9, 'Other', 'Other');

/* update old device types */
update `devices` set type=9 where type=8;
update `devices` set type=8 where type=7;
update `devices` set type=7 where type=6;
update `devices` set type=6 where type=5;
update `devices` set type=5 where type=4;
update `devices` set type=4 where type=3;
update `devices` set type=3 where type=2;
update `devices` set type=2 where type=1;
update `devices` set type=1 where type=0;


/* extend section name  */
ALTER TABLE `sections` CHANGE `name` `name` VARCHAR(128)  CHARACTER SET utf8  NOT NULL  DEFAULT '';
