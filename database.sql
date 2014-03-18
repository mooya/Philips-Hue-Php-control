--
-- Tabelstructuur voor tabel `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  `group_order` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `group_members`
--

CREATE TABLE IF NOT EXISTS `group_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `light_id` int(11) NOT NULL,
  `light_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `scenes`
--

CREATE TABLE IF NOT EXISTS `scenes` (
  `scene_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `scene_name` varchar(255) NOT NULL,
  `scene_image` varchar(255) NOT NULL,
  `scene_order` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`scene_id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `scene_pages`
--

CREATE TABLE IF NOT EXISTS `scene_pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) NOT NULL,
  `page_order` int(11) NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `scene_settings`
--

CREATE TABLE IF NOT EXISTS `scene_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scene_id` int(11) NOT NULL,
  `light_id` int(11) NOT NULL,
  `state` set('on','off') NOT NULL,
  `colormode` set('hs','xy','ct') NOT NULL,
  `brightness` int(11) NOT NULL,
  `hue` int(11) NOT NULL,
  `saturation` int(11) NOT NULL,
  `color_temp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `scene_id` (`scene_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
