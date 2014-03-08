/* Update from v 0.89 to 0.891 **/
ALTER TABLE `settings` CHANGE `version` `version` VARCHAR(5)  CHARACTER SET utf8  NULL  DEFAULT NULL;
UPDATE `settings` set `version` = '0.891'; 	/* UPDATE version */
/* add domain account */
ALTER TABLE `settingsDomain` ADD `adminUsername` VARCHAR(64)  NULL  DEFAULT NULL  AFTER `ad_port`;
ALTER TABLE `settingsDomain` ADD `adminPassword` VARCHAR(64)  NULL  DEFAULT NULL  AFTER `adminUsername`;
