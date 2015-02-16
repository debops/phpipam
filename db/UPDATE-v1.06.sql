/* Update from v 1.05 to 1.06 **/
UPDATE `settings` set `version` = '1.06'; 	/* UPDATE version */
/* add prettylinks field */
ALTER TABLE `settings` ADD `prettyLinks` SET("Yes","No")  NOT NULL  DEFAULT 'No'  AFTER `scanMaxThreads`;