/* Update from v 0.99 to 1.0 **/
UPDATE `settings` set `version` = '1.0'; 	/* UPDATE version */
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* clear verification */
UPDATE `settings` set `dbverified` = '0';
/* remove tooltips from admin */
ALTER TABLE `settings` DROP `showTooltips`;
/* fix IDs to int */
ALTER TABLE `subnets` CHANGE `sectionId` `sectionId` INT(11)  UNSIGNED  NULL  DEFAULT NULL;
ALTER TABLE `subnets` CHANGE `vrfId` `vrfId` INT(11)  UNSIGNED  NULL  DEFAULT NULL;
ALTER TABLE `subnets` CHANGE `masterSubnetId` `masterSubnetId` INT(11)  UNSIGNED  NULL  DEFAULT NULL;
ALTER TABLE `subnets` CHANGE `vlanId` `vlanId` INT(11)  UNSIGNED  NULL  DEFAULT NULL;
ALTER TABLE `subnets` CHANGE `isFolder` `isFolder` BOOL  NULL  DEFAULT '0';
ALTER TABLE `subnets` CHANGE `pingSubnet` `pingSubnet` BOOL  NULL  DEFAULT '0';

ALTER TABLE `ipaddresses` CHANGE `subnetId` `subnetId` INT(11)  UNSIGNED  NULL  DEFAULT NULL;
ALTER TABLE `ipaddresses` CHANGE `switch` `switch` INT(11)  UNSIGNED  NULL  DEFAULT NULL;

ALTER TABLE `requests` CHANGE `subnetId` `subnetId` INT(11)  UNSIGNED  NULL  DEFAULT NULL;

ALTER TABLE `sections` CHANGE `strictMode` `strictMode` BINARY(1)  NOT NULL  DEFAULT '0';
ALTER TABLE `sections` CHANGE `showVLAN` `showVLAN` BOOL  NOT NULL  DEFAULT '0';
ALTER TABLE `sections` CHANGE `showVRF` `showVRF` BOOL  NOT NULL  DEFAULT '0';

ALTER TABLE `users` CHANGE `lang` `lang` INT(11) UNSIGNED  NULL  DEFAULT '1';
