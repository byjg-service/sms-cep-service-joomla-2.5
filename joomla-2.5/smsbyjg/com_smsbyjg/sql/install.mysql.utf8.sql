CREATE TABLE `#__byjg_groups` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(254) NOT NULL default '',
	`ownerid` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);

CREATE TABLE `#__byjg_joomlauser` (
	`id` int(11) NOT NULL auto_increment,
	`userid` int(11) NOT NULL default '0',
	`number` varchar(254) NOT NULL default '',
	`comment` varchar(254) NOT NULL default '',
	`state` tinyint(4) NOT NULL default '0',
	`credits` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `userid` (`userid`)
);

CREATE TABLE `#__byjg_phonebook` (
	`id` int(11) NOT NULL auto_increment,
	`ownerid` int(11) NOT NULL default '0',
	`number` varchar(254) NOT NULL default '',
	`name` varchar(254) NOT NULL default '',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `id` (`id`)
);

CREATE TABLE `#__byjg_provider` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(254) NOT NULL default '',
	`file` varchar(254) NOT NULL default '',
	`params` longtext NOT NULL,
	`active` enum('0','1') NOT NULL default '0',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `id` (`id`)
);

CREATE TABLE `#__byjg_sendsms` (
	`id` int(11) NOT NULL auto_increment,
	`userid` int(11) NOT NULL default '0',
	`senddate` timestamp NOT NULL,
	`text` varchar(254) NOT NULL default '',
	`from` varchar(254) NOT NULL default '',
	`to` varchar(254) NOT NULL default '',
	`providerid` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);

CREATE TABLE `#__byjg_usergroups` (
	`id` int(11) NOT NULL auto_increment,
	`memberid` int(11) NOT NULL default '0',
	`groupid` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `id` (`id`)
);

CREATE TABLE `#__byjg_config` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(254) NOT NULL default '',
  `value` text NOT NULL
);

