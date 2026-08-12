-- UPDATE TO VERSION 1.1.0

--
-- Table structure for table `#__gs_tickets_department_to_forms`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_department_to_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL,
  `form_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;