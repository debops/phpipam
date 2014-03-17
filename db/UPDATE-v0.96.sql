/* Update from v 0.95 to 0.96 **/
UPDATE `settings` set `version` = '0.96'; 	/* UPDATE version */
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* add additional sizes to widgets */
ALTER TABLE `widgets` CHANGE `wsize` `wsize` SET('4','6','8','12')  CHARACTER SET utf8  NOT NULL  DEFAULT '6';
UPDATE `widgets` set `wsize` = '6';
/* Change size for specific widgets */
UPDATE `widgets` set `wsize`='4' where `wid` = 1;
UPDATE `widgets` set `wsize`='8' where `wid` = 2;
UPDATE `widgets` set `wsize`='12' where `wid` = 6;
UPDATE `widgets` set `whref`='yes' where `wid`=3 or`wid`=4 or`wid`=5 or `wid`=6 or `wid`=7 or `wid`=8 or `wid`=9;
/* update users */
UPDATE `users` set `widgets` = 'statistics;favourite_subnets;changelog;access_logs;error_logs;top10_hosts_v4' where `role` = "Administrator";
UPDATE `users` set `widgets` = 'statistics;favourite_subnets;changelog;top10_hosts_v4' where `role` = "User";