
CREATE TABLE IF NOT EXISTS `login` (
  `id_login` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nivel` int(11) DEFAULT 3,
  `nome` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT '',
  `login` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `senha` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id_login`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DELETE FROM `login`;

INSERT INTO `login` (`id_login`, `nivel`, `nome`, `email`, `login`, `senha`) VALUES
	(1, 1, 'DVA Teste', 'dva@augustobraun.com', 'usuario', 'e8d95a51f3af4a3b134bf6bb680a213a');
