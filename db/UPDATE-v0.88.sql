/* Update from v 0.86 to 0.87 **/
UPDATE `settings` set `version` = '0.88'; 	/* UPDATE version */
/* add master section for subsections */
ALTER TABLE `sections` ADD `masterSection` INT(11)  NULL  DEFAULT '0'  AFTER `description`;
