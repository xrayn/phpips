CREATE TABLE [loggertable] (
[id] INTEGER  NOT NULL PRIMARY KEY,
[time] TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
[affected_tags] VARCHAR(256)  NULL,
[impact] INTEGER  NOT NULL,
[session_impact] INTEGER  NOT NULL,
[attacker_ip] VARCHAR(128)
);
