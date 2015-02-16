/* Update from v 1.07 to 1.08 */
UPDATE `settings` set `version` = '1.08';

/* add max session duration field to settings */
ALTER TABLE `settings` ADD `inactivityTimeout` INT(5)  NOT NULL  DEFAULT '3600'  AFTER `hiddenCustomFields`;

/* add option to subnets to discover from cron script */
ALTER TABLE `subnets` ADD `discoverSubnet` BINARY(1)  NULL  DEFAULT '0'  AFTER `pingSubnet`;