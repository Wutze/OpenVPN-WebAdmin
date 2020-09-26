DROP TABLE IF EXISTS `user_ip`;
CREATE TABLE `user_ip` (
  `ipid` int(11) NOT NULL,
  `uid` int(10) NOT NULL,
  `user_ip` varchar(15) NOT NULL,
  `netmask` varchar(15) NOT NULL,
  `server_ip` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `user_ip`
--
ALTER TABLE `user_ip`
  ADD UNIQUE KEY `ipid` (`ipid`),
  ADD UNIQUE KEY `user_ip` (`user_ip`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD UNIQUE KEY `server_ip` (`server_ip`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `user_ip`
--
ALTER TABLE `user_ip`
  MODIFY `ipid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;