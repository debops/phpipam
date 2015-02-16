/* Update from v 1.06 to 1.07 **/
UPDATE `settings` set `version` = '1.07'; 	/* UPDATE version */
/* add option to hide free space */
ALTER TABLE `settings` ADD `hideFreeRange` tinyint(1) DEFAULT '0'  AFTER `prettyLinks`;
/* add option for max vlan number */
ALTER TABLE `settings` ADD `vlanMax` INT(8)  NULL  DEFAULT '4096'  AFTER `vlanDuplicate`;
/* add hidden custom fields */
ALTER TABLE `settings` ADD `hiddenCustomFields` VARCHAR(1024)  NULL  DEFAULT NULL  AFTER `hideFreeRange`;