/*
SQLyog Community v12.5.1 (64 bit)
MySQL - 10.1.19-MariaDB : Database - dbbookstore
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`dbbookstore` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dbbookstore`;

/*Table structure for table `books` */

DROP TABLE IF EXISTS `books`;

CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_name` varchar(500) NOT NULL,
  `author_name` varchar(500) NOT NULL,
  `book_status` enum('1','0') NOT NULL,
  `issued_date` datetime DEFAULT NULL,
  `return_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `books` */

insert  into `books`(`id`,`book_name`,`author_name`,`book_status`,`issued_date`,`return_date`) values 
(1,'Rich Dad poor dad','Rony Roy','0','2019-05-29 08:00:44','2019-05-29 08:22:25'),
(2,'Sham chi aai','Sane Guruji','0','2019-05-29 08:21:36','2019-05-29 08:22:40'),
(3,'Bagwat Geeta','Waly Koli','1','2019-05-29 08:21:34','0000-00-00 00:00:00'),
(4,'Tipur chandne','Mohan Jadhav','1','2019-05-29 08:23:01','0000-00-00 00:00:00'),
(6,'7 Secret','Ali Jafar','1','2019-05-29 08:22:52','0000-00-00 00:00:00');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
