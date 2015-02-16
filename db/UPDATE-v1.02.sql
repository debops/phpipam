/* Update from v 1.01 to 1.02 **/
UPDATE `settings` set `version` = '1.02'; 	/* UPDATE version */
/* add new index to subnetId in IP addresses field */
ALTER TABLE `ipaddresses` ADD INDEX `subnetid` (`subnetId`);
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* add mail notification for all changes */
ALTER TABLE `users` ADD `mailNotify` SET('Yes','No')  NULL  DEFAULT 'No'  AFTER `favourite_subnets`;