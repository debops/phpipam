/* Update from v 0.97 to 0.98 **/
UPDATE `settings` set `version` = '0.98'; 	/* UPDATE version */
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* clear verification */
/* UPDATE `settings` set `dbverified` = '0'; */
/* set db verified flag */
ALTER TABLE `settings` ADD `dbverified` BINARY(1)  NOT NULL  DEFAULT '0'  AFTER `version`;