/* Update from v 1.08 to 1.09 */
UPDATE `settings` set `version` = '1.09';

/* add comment to API  */
ALTER TABLE `api` ADD `app_comment` TEXT  NULL;