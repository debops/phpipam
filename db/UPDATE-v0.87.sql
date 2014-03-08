/* Update from v 0.86 to 0.87 **/
UPDATE `settings` set `version` = '0.87'; 	/* UPDATE version */
/* add showVLAN and showVRF to sections */
ALTER TABLE `sections` ADD `showVLAN` BOOL  NOT NULL  DEFAULT '0';
ALTER TABLE `sections` ADD `showVRF` BOOL  NOT NULL  DEFAULT '0';
/* gernam lanuguage */
INSERT into `lang` (`l_code`,`l_name`) VALUES ('de_DE','Deutsch');