-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: localhost    Database: Datasikkerhet
-- ------------------------------------------------------
-- Server version	8.0.41-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `brukere`
--

DROP TABLE IF EXISTS `brukere`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brukere` (
  `id` int NOT NULL AUTO_INCREMENT,
  `navn` varchar(255) NOT NULL,
  `e_post` varchar(255) NOT NULL,
  `passord_hash` varchar(255) NOT NULL,
  `rolle` enum('student','foreleser') NOT NULL,
  `bilde` varchar(255) DEFAULT NULL,
  `studieretning` varchar(255) DEFAULT NULL,
  `studiekull` year DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `e_post` (`e_post`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brukere`
--

LOCK TABLES `brukere` WRITE;
/*!40000 ALTER TABLE `brukere` DISABLE KEYS */;
INSERT INTO `brukere` VALUES (1,'Miran Nihad','miran@hotmail.com','$2y$10$y3wmUf1v3c/qvT8MxPoXLO30i9MZIokKR6nMlQr1It2UORFLBlGVC','student',NULL,NULL,NULL),(2,'etter hacking','etterhacking@hotmail.com','$2y$10$r4MNvF7Pk9rmD2MJwzrlv.LNxXTptqylME9wD/MB91Rfl7f0IgPRO','student',NULL,NULL,NULL),(3,'Jens Jensen','jens@jensen.com','$2y$10$z0y3REKEw6H6MNz1ERCmxOf0TmFUma8FPdnRDhhkEp9W8vVXUjIxy','student',NULL,NULL,NULL);
/*!40000 ALTER TABLE `brukere` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emner`
--

DROP TABLE IF EXISTS `emner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `emnekode` varchar(50) NOT NULL,
  `emnenavn` varchar(255) NOT NULL,
  `foreleser_id` int NOT NULL,
  `pin_kode` char(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emnekode` (`emnekode`),
  KEY `foreleser_id` (`foreleser_id`),
  CONSTRAINT `emner_ibfk_1` FOREIGN KEY (`foreleser_id`) REFERENCES `brukere` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emner`
--

LOCK TABLES `emner` WRITE;
/*!40000 ALTER TABLE `emner` DISABLE KEYS */;
/*!40000 ALTER TABLE `emner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kommentarer`
--

DROP TABLE IF EXISTS `kommentarer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kommentarer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `melding_id` int NOT NULL,
  `innhold` text NOT NULL,
  `dato_opprettet` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `melding_id` (`melding_id`),
  CONSTRAINT `kommentarer_ibfk_1` FOREIGN KEY (`melding_id`) REFERENCES `meldinger` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kommentarer`
--

LOCK TABLES `kommentarer` WRITE;
/*!40000 ALTER TABLE `kommentarer` DISABLE KEYS */;
/*!40000 ALTER TABLE `kommentarer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meldinger`
--

DROP TABLE IF EXISTS `meldinger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meldinger` (
  `id` int NOT NULL AUTO_INCREMENT,
  `emne_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `innhold` text NOT NULL,
  `dato_opprettet` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `emne_id` (`emne_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `meldinger_ibfk_1` FOREIGN KEY (`emne_id`) REFERENCES `emner` (`id`) ON DELETE CASCADE,
  CONSTRAINT `meldinger_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `brukere` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meldinger`
--

LOCK TABLES `meldinger` WRITE;
/*!40000 ALTER TABLE `meldinger` DISABLE KEYS */;
/*!40000 ALTER TABLE `meldinger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rapporterte_meldinger`
--

DROP TABLE IF EXISTS `rapporterte_meldinger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rapporterte_meldinger` (
  `id` int NOT NULL AUTO_INCREMENT,
  `melding_id` int NOT NULL,
  `grunn` text NOT NULL,
  `dato_rapportert` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `melding_id` (`melding_id`),
  CONSTRAINT `rapporterte_meldinger_ibfk_1` FOREIGN KEY (`melding_id`) REFERENCES `meldinger` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rapporterte_meldinger`
--

LOCK TABLES `rapporterte_meldinger` WRITE;
/*!40000 ALTER TABLE `rapporterte_meldinger` DISABLE KEYS */;
/*!40000 ALTER TABLE `rapporterte_meldinger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `svar`
--

DROP TABLE IF EXISTS `svar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `svar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `melding_id` int NOT NULL,
  `foreleser_id` int NOT NULL,
  `innhold` text NOT NULL,
  `dato_opprettet` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `melding_id` (`melding_id`),
  KEY `foreleser_id` (`foreleser_id`),
  CONSTRAINT `svar_ibfk_1` FOREIGN KEY (`melding_id`) REFERENCES `meldinger` (`id`) ON DELETE CASCADE,
  CONSTRAINT `svar_ibfk_2` FOREIGN KEY (`foreleser_id`) REFERENCES `brukere` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `svar`
--

LOCK TABLES `svar` WRITE;
/*!40000 ALTER TABLE `svar` DISABLE KEYS */;
/*!40000 ALTER TABLE `svar` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-02 12:41:40
