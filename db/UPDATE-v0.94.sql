/* Update from v 0.93 to 0.94 **/
UPDATE `settings` set `version` = '0.94'; 	/* UPDATE version */
/* change requests */
ALTER TABLE `requests` CHANGE `requester` `requester` VARCHAR(128) DEFAULT NULL;
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* add ping parameter to settings table */
ALTER TABLE `settings` ADD `scanPingPath` VARCHAR(64)  NULL  DEFAULT '/bin/ping'  AFTER `enableChangelog`;
ALTER TABLE `settings` ADD `scanMaxThreads` INT(4)  NULL  DEFAULT '128'  AFTER `scanPingPath`;
