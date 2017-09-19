/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for pringles
CREATE DATABASE IF NOT EXISTS `pringles` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `pringles`;

-- Dumping structure for table pringles.users
CREATE TABLE IF NOT EXISTS `users` (
  `UID` smallint(6) NOT NULL AUTO_INCREMENT,
  `Username` varchar(64) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Admin` tinyint(4) NOT NULL DEFAULT '0',
  `Status` tinyint(4) NOT NULL DEFAULT '0',
  `Plan` varchar(50) DEFAULT NULL,
  `HWID` varchar(32) DEFAULT NULL,
  `Expire` varchar(15) DEFAULT NULL,
  `Config` mediumtext,
  `Lastlogin` varchar(50) DEFAULT NULL,
  `Lastip` varchar(50) DEFAULT NULL,
  `Failedip` mediumtext,
  `FailedConfig` mediumtext,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table pringles.users: ~2 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
REPLACE INTO `users` (`UID`, `Username`, `Password`, `Admin`, `Status`, `Plan`, `HWID`, `Expire`, `Config`, `Lastlogin`, `Lastip`, `Failedip`, `FailedConfig`) VALUES
	(1, 'Smug', NULL, 1, 1, '2', NULL, '3137966017', NULL, NULL, NULL, NULL, NULL),
	(2, 'Jirx', NULL, 1, 1, '2', NULL, '3137966017', NULL, NULL, NULL, NULL, NULL),
	(3, 'Jogn', NULL, 0, 1, '2', NULL, '3137966017', NULL, NULL, NULL, NULL, NULL),
	(4, 'Sub', NULL, 0, 1, '2', NULL, '3137966017', NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
