/* Update from v 0.9 to 0.91 **/
UPDATE `settings` set `version` = '0.91'; 	/* UPDATE version */
/* add favourite subnets */
ALTER TABLE `users` ADD `favourite_subnets` VARCHAR(1024)  NULL  DEFAULT NULL  AFTER `lang`;
update `users` set `widgets` = concat('favourite_subnets;', `widgets`);
