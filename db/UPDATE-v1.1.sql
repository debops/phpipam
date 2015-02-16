/* Update from v 1.09 to 1.1 */
UPDATE `settings` set `version` = '1.1';

/* reset donations */
UPDATE `settings` set `donate` = '0'; 

/* add ssl/tls option for smtp mailer */
ALTER TABLE `settingsMail` ADD `msecure` SET('none','ssl','tls')  NOT NULL  DEFAULT 'none'  AFTER `mtype`;
