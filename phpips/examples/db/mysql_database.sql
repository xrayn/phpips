CREATE TABLE  `PHPIPS`.`LOGTABLE` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`impact` INT NOT NULL ,
`session_impact` INT NOT NULL ,
`attacker_ip` VARCHAR( 30 ) NOT NULL ,
`affected_tags` VARCHAR( 255 ) NOT NULL ,
`jsonevent` TEXT NOT NULL ,
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;

