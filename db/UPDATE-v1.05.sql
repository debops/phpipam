/* Update from v 1.04 to 1.05 **/
UPDATE `settings` set `version` = '1.05'; 	/* UPDATE version */
/* change password field for crypted passwords */
ALTER TABLE `users` CHANGE `password` `password` CHAR(128)  COLLATE utf8_bin DEFAULT NULL;