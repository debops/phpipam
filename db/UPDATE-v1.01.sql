/* Update from v 1.00 to 1.01 **/
UPDATE `settings` set `version` = '1.01'; 	/* UPDATE version */
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* add es_ES translation */
INSERT INTO `lang` (`l_code`, `l_name`) VALUES ('es_ES', 'Español');