/* Update from v 0.94 to 0.95 **/
UPDATE `settings` set `version` = '0.95'; 	/* UPDATE version */
/* reset donations */
UPDATE `settings` set `donate` = '0'; 
/* create widget table */
CREATE TABLE `widgets` (
  `wid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wtitle` varchar(64) NOT NULL DEFAULT '',
  `wdescription` varchar(1024) DEFAULT NULL,
  `wfile` varchar(64) NOT NULL DEFAULT '',
  `wparams` varchar(1024) DEFAULT NULL,
  `whref` set('yes','no') NOT NULL DEFAULT 'no',
  `wsize` set('50','100') NOT NULL DEFAULT '50',
  `wadminonly` set('yes','no') NOT NULL DEFAULT 'no',
  `wactive` set('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`wid`)
) DEFAULT CHARSET=utf8;
/* and insert widgets */
INSERT INTO `widgets` (`wid`, `wtitle`, `wdescription`, `wfile`, `wparams`, `whref`, `wsize`, `wadminonly`, `wactive`)
VALUES
	(1, 'Statistics', 'Shows some statistics on number of hosts, subnets', 'statistics', NULL, 'no', '50', 'no', 'yes'),
	(2, 'Favourite subnets', 'Shows 5 favourite subnets', 'favourite_subnets', NULL, 'no', '50', 'no', 'yes'),
	(3, 'Top 10 IPv4 subnets by number of hosts', 'Shows graph of top 10 IPv4 subnets by number of hosts', 'top10_hosts_v4', NULL, 'no', '50', 'no', 'yes'),
	(4, 'Top 10 IPv6 subnets by number of hosts', 'Shows graph of top 10 IPv6 subnets by number of hosts', 'top10_hosts_v6', NULL, 'no', '50', 'no', 'yes'),
	(5, 'Top 10 IPv4 subnets by usage percentage', 'Shows graph of top 10 IPv4 subnets by usage percentage', 'top10_percentage', NULL, 'no', '50', 'no', 'yes'),
	(6, 'Last 5 change log entries', 'Shows last 5 change log entries', 'changelog', NULL, 'no', '50', 'no', 'yes'),
	(7, 'Active IP addresses requests', 'Shows list of active IP address request', 'requests', NULL, 'no', '50', 'yes', 'yes'),
	(8, 'Last 5 informational logs', 'Shows list of last 5 informational logs', 'access_logs', NULL, 'no', '50', 'yes', 'yes'),
	(9, 'Last 5 warning / error logs', 'Shows list of last 5 warning and error logs', 'error_logs', NULL, 'no', '50', 'yes', 'yes');
