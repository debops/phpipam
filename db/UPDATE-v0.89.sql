/* Update from v 0.88 to 0.89 **/
UPDATE `settings` set `version` = '0.89'; 	/* UPDATE version */
/* add widgets to users */
ALTER TABLE `users` ADD `widgets` VARCHAR(1024)  NULL  DEFAULT 'statistics;top10_hosts_v4;top10_hosts_v6;top10_percentage' AFTER `domainUser`;
UPDATE `users` set `widgets` = 'statistics;top10_hosts_v4;top10_hosts_v6;top10_percentage;access_logs;error_logs' WHERE `role` = 'Administrator';