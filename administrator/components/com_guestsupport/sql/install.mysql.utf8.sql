--
-- Table structure for table `#__gs_tickets`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `short_ticket_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ticket_token` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `form_id` int(11) DEFAULT NULL,
  `ticket_email_hash` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `agent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `message_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ticket or reply',
  `department_id` int(11) NOT NULL COMMENT 'pre or support',
  `subject` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_fields` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `encrypted_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'open, pending, closed',
  `autoreply` int(11) DEFAULT 0,
  `published` int(11) NOT NULL DEFAULT 1,
  `update_user_id` int(11) DEFAULT 0,
  `user_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'user or agent',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ticket_timezone` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_attachments`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_attachments` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_ticket_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_ticket_message_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_name_raw` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_name_enc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_size` int(11) NOT NULL,
  `file_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_config`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_config` (
  `name` varchar(200) NOT NULL DEFAULT '',
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_departments`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_departments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `agent_id` int(11) NOT NULL,
  `additional_emails` text DEFAULT NULL,
  `additional_emails_type` varchar(50) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_email_templates`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_email_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(400) NOT NULL DEFAULT '',
  `type` varchar(100) NOT NULL DEFAULT '',
  `subject` text NOT NULL,
  `template` text NOT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `active` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_forms`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_forms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_name` varchar(200) NOT NULL DEFAULT '',
  `form_fields` text NOT NULL,
  `form_type` varchar(50) DEFAULT NULL,
  `recaptcha` int(11) DEFAULT NULL,
  `input_class` varchar(100) DEFAULT NULL,
  `submit_text` varchar(200) DEFAULT NULL,
  `verify_otp_text` varchar(200) DEFAULT NULL,
  `submit_class` varchar(200) DEFAULT NULL,
  `fileupload` int(11) DEFAULT NULL,
  `upload_filelimit` int(11) DEFAULT NULL,
  `upload_filesize` int(11) DEFAULT NULL,
  `allowed_filetypes` varchar(400) DEFAULT NULL,
  `fileupload_label` varchar(200) DEFAULT NULL,
  `fileupload_help` varchar(400) DEFAULT NULL,
  `verify_email` int(11) DEFAULT NULL,
  `suggest_docs` int(11) DEFAULT NULL,
  `suggest_docs_cats` text DEFAULT NULL,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_otp`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_otp` (
  `otp` varchar(200) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL DEFAULT '',
  `auth` varchar(200) NOT NULL DEFAULT '',
  `created` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_otp_history`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_otp_history` (
  `email` varchar(200) NOT NULL DEFAULT '',
  `created` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_user_settings`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_user_settings` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `picture` varchar(200) DEFAULT '',
  `signature` varchar(400) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__gs_tickets_department_to_forms`
--

CREATE TABLE IF NOT EXISTS `#__gs_tickets_department_to_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL,
  `form_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
