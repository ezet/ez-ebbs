DROP TABLE IF EXISTS `User`;
DROP TABLE if EXISTS `Blogpost`;
DROP TABLE if EXISTS `Comment`;
DROP TABLE IF EXISTS `File`;

CREATE TABLE `User` (
`UserId` int unsigned NOT NULL auto_increment,
`Username` varchar(12) NOT NULL,
`Password` varchar(74) NOT NULL,
`FirstName` varchar(127) NOT NULL,
`LastName` varchar(127) NOT NULL,
`Email` varchar(127) NOT NULL,
`WebUrl` varchar(127) NOT NULL,
`About` varchar(1023) NOT NULL,
`CountryId` int unsigned NOT NULL,
`BlogTitle` varchar(127) NOT NULL,
`Template` enum ('default', 'sunset') NOT NULL default 'default',
`ProfileImage` varchar(255) NOT NULL,
`LastLogin` TIMESTAMP default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
`LoginCount` int unsigned NOT NULL,
`Created` timestamp NOT NULL default 0,
`Blocked` bool NOT NULL,
`Admin` bool NOT NULL,
`FlaggedCount` int unsigned NOT NULL,   
PRIMARY KEY (`UserID`)
-- UNIQUE KEY `Username` (`Username`),
-- UNIQUE KEY `Email` (`Email)
) default charset=utf8;



CREATE TABLE `Blogpost` (
`BlogpostId` int unsigned NOT NULL auto_increment,
`UserId` int unsigned NOT NULL,
`Title` varchar(255) NOT NULL,
`Text` blob NOT NULL,
`Created` timestamp NOT NULL default 0,
`Modified` timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
`ViewCount` smallint unsigned NOT NULL,
`Hidden` BOOL NOT NULL,
`Flagged` BOOL NOT NULL,
`Deleted` int(1) NOT NULL,
PRIMARY KEY (`BlogpostID`)
) default charset=utf8;



CREATE TABLE `Comment` (
`CommentId` int unsigned NOT NULL auto_increment,
`BlogpostId` int unsigned NOT NULL,
`UserId` int unsigned NOT NULL,
`Author` varchar(127) NOT NULL,
`Email` varchar(127) NOT NULL,
`WebUrl` varchar(127) NOT NULL,
`Text` blob NOT NULL,
`Created` timestamp default 0,
`Modified` timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
`Flagged` BOOL NOT NULL,
`Deleted` int(1) NOT NULL,
PRIMARY KEY (`CommentId`)
) default charset=utf8;



CREATE TABLE `File` (
`FileId` int unsigned NOT NULL auto_increment,
`UserId` int unsigned NOT NULL,
`File` mediumblob NOT NULL,
`Created` timestamp default 0,
`Modified` timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
PRIMARY KEY (`FileId`)
) default charset=utf8;