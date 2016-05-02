-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: db10.cs.pitt.edu
-- Generation Time: Apr 27, 2016 at 09:58 AM
-- Server version: 5.0.77
-- PHP Version: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `new_astroshelf`
--

-- --------------------------------------------------------

--
-- Table structure for table `annotation`
--

DROP TABLE IF EXISTS `annotation`;
CREATE TABLE IF NOT EXISTS `annotation` (
  `anno_id` bigint(20) unsigned NOT NULL auto_increment,
  `anno_type_id` int(10) unsigned NOT NULL,
  `anno_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `anno_value` text collate utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `target_type` enum('object','annotation','area/point','view','set') collate utf8_unicode_ci NOT NULL,
  `access_level` enum('PRIVATE','SHARED','PUBLIC') collate utf8_unicode_ci NOT NULL default 'SHARED',
  `ts_created` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'For versioning, create time',
  `ts_deleted` timestamp NULL default NULL COMMENT 'For versioning, delete time',
  PRIMARY KEY  (`anno_id`),
  KEY `fk_user_id` (`user_id`),
  KEY `fk_anno_type_id` (`anno_type_id`),
  KEY `anno_type_id` (`anno_type_id`),
  KEY `anno_title` (`anno_title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database' AUTO_INCREMENT=54489 ;

-- --------------------------------------------------------

--
-- Table structure for table `annotation_access_control`
--

DROP TABLE IF EXISTS `annotation_access_control`;
CREATE TABLE IF NOT EXISTS `annotation_access_control` (
  `annotation_access_id` int(10) unsigned NOT NULL auto_increment,
  `annotation_id` int(10) unsigned default NULL,
  `create_access` int(11) NOT NULL,
  `modify_access` int(11) NOT NULL,
  `delete_access` int(11) NOT NULL,
  `view_access` int(11) NOT NULL,
  `operator_type` enum('user','group','all_users','public') collate utf8_unicode_ci NOT NULL,
  `operator_id` int(11) unsigned default NULL,
  PRIMARY KEY  (`annotation_access_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Defines annotation access control' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `anno_for_group`
--

DROP TABLE IF EXISTS `anno_for_group`;
CREATE TABLE IF NOT EXISTS `anno_for_group` (
  `anno_for_group_id` bigint(20) unsigned NOT NULL auto_increment,
  `anno_src_id` bigint(20) unsigned NOT NULL,
  `group_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`anno_for_group_id`),
  KEY `fk_anno_src_id` (`anno_src_id`),
  KEY `fk_group_tar_id` (`group_tar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `anno_for_user`
--

DROP TABLE IF EXISTS `anno_for_user`;
CREATE TABLE IF NOT EXISTS `anno_for_user` (
  `anno_for_user_id` bigint(20) unsigned NOT NULL auto_increment,
  `anno_src_id` bigint(20) unsigned NOT NULL,
  `user_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`anno_for_user_id`),
  KEY `fk_anno_src_id` (`anno_src_id`),
  KEY `fk_user_tar_id` (`user_tar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `anno_to_anno`
--

DROP TABLE IF EXISTS `anno_to_anno`;
CREATE TABLE IF NOT EXISTS `anno_to_anno` (
  `anno_to_anno_id` bigint(20) unsigned NOT NULL auto_increment,
  `anno_src_id` bigint(20) unsigned NOT NULL,
  `anno_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`anno_to_anno_id`),
  UNIQUE KEY `fk_anno_src_id` (`anno_src_id`),
  KEY `fk_anno_tar_id` (`anno_tar_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `anno_to_area_point`
--

DROP TABLE IF EXISTS `anno_to_area_point`;
CREATE TABLE IF NOT EXISTS `anno_to_area_point` (
  `anno_to_area_point_id` bigint(20) unsigned NOT NULL auto_increment,
  `anno_src_id` bigint(20) unsigned NOT NULL,
  `RA_bl` float NOT NULL,
  `Dec_bl` float NOT NULL,
  `type_bl` enum('type1-1950','type2-2000') collate utf8_unicode_ci NOT NULL,
  `RA_tr` float default NULL,
  `Dec_tr` float default NULL,
  `type_tr` enum('type1-1950','type2-2000') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`anno_to_area_point_id`),
  UNIQUE KEY `fk_anno_src_id` (`anno_src_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `anno_to_obj`
--

DROP TABLE IF EXISTS `anno_to_obj`;
CREATE TABLE IF NOT EXISTS `anno_to_obj` (
  `anno_src_id` bigint(20) unsigned NOT NULL,
  `obj_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`anno_src_id`,`obj_tar_id`),
  KEY `fk_anno_src_id` (`anno_src_id`),
  KEY `fk_obj_tar_id` (`obj_tar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database';

-- --------------------------------------------------------

--
-- Table structure for table `anno_to_set`
--

DROP TABLE IF EXISTS `anno_to_set`;
CREATE TABLE IF NOT EXISTS `anno_to_set` (
  `anno_to_set_id` bigint(20) unsigned NOT NULL auto_increment,
  `anno_src_id` bigint(20) unsigned NOT NULL,
  `set_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`anno_to_set_id`),
  UNIQUE KEY `fk_anno_src_id` (`anno_src_id`),
  KEY `fk_set_tar_id` (`set_tar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `anno_to_view`
--

DROP TABLE IF EXISTS `anno_to_view`;
CREATE TABLE IF NOT EXISTS `anno_to_view` (
  `anno_to_view_id` bigint(20) unsigned NOT NULL auto_increment,
  `anno_src_id` bigint(20) unsigned NOT NULL,
  `view_name` varchar(45) collate utf8_unicode_ci NOT NULL,
  `query` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`anno_to_view_id`),
  UNIQUE KEY `fk_anno_src_id` (`anno_src_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `anno_type`
--

DROP TABLE IF EXISTS `anno_type`;
CREATE TABLE IF NOT EXISTS `anno_type` (
  `anno_type_id` int(10) unsigned NOT NULL auto_increment,
  `anno_type_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`anno_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `Attribute`
--

DROP TABLE IF EXISTS `Attribute`;
CREATE TABLE IF NOT EXISTS `Attribute` (
  `att_id` bigint(20) unsigned NOT NULL auto_increment,
  `att_name` varchar(45) default NULL,
  `att_alias` varchar(30) NOT NULL,
  PRIMARY KEY  (`att_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

DROP TABLE IF EXISTS `bookmark`;
CREATE TABLE IF NOT EXISTS `bookmark` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `type` enum('obj','loc','anno') collate utf8_unicode_ci NOT NULL,
  `ts_created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id+type` (`user_id`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=97 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark_of_anno`
--

DROP TABLE IF EXISTS `bookmark_of_anno`;
CREATE TABLE IF NOT EXISTS `bookmark_of_anno` (
  `bookmark_id` bigint(20) unsigned NOT NULL,
  `anno_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`bookmark_id`,`anno_id`),
  KEY `bookmark_of_anno_ibfk_2` (`anno_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark_of_loc`
--

DROP TABLE IF EXISTS `bookmark_of_loc`;
CREATE TABLE IF NOT EXISTS `bookmark_of_loc` (
  `bookmark_id` bigint(20) unsigned NOT NULL,
  `_RA_` float NOT NULL,
  `_DEC_` float NOT NULL,
  PRIMARY KEY  (`bookmark_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark_of_obj`
--

DROP TABLE IF EXISTS `bookmark_of_obj`;
CREATE TABLE IF NOT EXISTS `bookmark_of_obj` (
  `bookmark_id` bigint(20) unsigned NOT NULL,
  `obj_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`bookmark_id`,`obj_id`),
  KEY `bookmark_of_obj_ibfk_2` (`obj_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_info`
--

DROP TABLE IF EXISTS `group_info`;
CREATE TABLE IF NOT EXISTS `group_info` (
  `group_id` bigint(20) unsigned NOT NULL auto_increment,
  `group_name` varchar(45) collate utf8_unicode_ci NOT NULL,
  `group_url` text collate utf8_unicode_ci NOT NULL,
  `group_description` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Database ' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_in_group`
--

DROP TABLE IF EXISTS `group_in_group`;
CREATE TABLE IF NOT EXISTS `group_in_group` (
  `group_in_group_id` bigint(20) unsigned NOT NULL auto_increment,
  `group_src_id` bigint(20) unsigned NOT NULL,
  `group_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`group_in_group_id`),
  KEY `fk_group_tar_id` (`group_tar_id`),
  KEY `fk_group_src_id` (`group_src_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Database' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `liveinterest`
--

DROP TABLE IF EXISTS `liveinterest`;
CREATE TABLE IF NOT EXISTS `liveinterest` (
  `interest_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `label` varchar(100) character set latin1 NOT NULL,
  `keyword` varchar(1000) character set latin1 default NULL,
  `ra_bl` double NOT NULL,
  `dec_bl` double NOT NULL,
  `ra_tr` double NOT NULL,
  `dec_tr` double NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY  (`interest_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=114 ;

-- --------------------------------------------------------

--
-- Table structure for table `lsst_temp`
--

DROP TABLE IF EXISTS `lsst_temp`;
CREATE TABLE IF NOT EXISTS `lsst_temp` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `visit_id` int(11) NOT NULL,
  `raft_ccd` int(11) NOT NULL,
  `url` varchar(200) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `visit_id` (`visit_id`),
  KEY `raft_ccd` (`raft_ccd`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16302 ;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `is_read` tinyint(4) NOT NULL,
  `starred` tinyint(4) NOT NULL,
  `notification_data` varchar(2000) character set latin1 NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_to_liveinterest`
--

DROP TABLE IF EXISTS `notification_to_liveinterest`;
CREATE TABLE IF NOT EXISTS `notification_to_liveinterest` (
  `interest_id` bigint(20) unsigned NOT NULL,
  `notification_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`interest_id`,`notification_id`),
  KEY `notification_id` (`notification_id`),
  KEY `interest_id` (`interest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Connects the liveinterest table with the notifications table';

-- --------------------------------------------------------

--
-- Table structure for table `object_access_control`
--

DROP TABLE IF EXISTS `object_access_control`;
CREATE TABLE IF NOT EXISTS `object_access_control` (
  `object_access_id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned default NULL,
  `survey_id` int(10) unsigned default NULL,
  `create_access` int(11) NOT NULL,
  `modify_access` int(11) NOT NULL,
  `delete_access` int(11) NOT NULL,
  `view_access` int(11) NOT NULL,
  `operator_type` enum('user','group','all_users','public') collate utf8_unicode_ci NOT NULL,
  `operator_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`object_access_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `object_info`
--

DROP TABLE IF EXISTS `object_info`;
CREATE TABLE IF NOT EXISTS `object_info` (
  `object_id` bigint(20) unsigned NOT NULL auto_increment,
  `survey_id` bigint(20) unsigned NOT NULL,
  `survey_obj_id` text collate utf8_unicode_ci NOT NULL,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `_RA_` float NOT NULL,
  `_DEC_` float NOT NULL,
  `RA_TYPE` enum('type1-1950','type2-2000') collate utf8_unicode_ci NOT NULL default 'type2-2000',
  `Z` double default NULL,
  `color` double default NULL,
  `magnitude` double default NULL,
  `obj_type` varchar(10) collate utf8_unicode_ci default NULL,
  `specClass` tinyint(4) default '0',
  PRIMARY KEY  (`object_id`),
  UNIQUE KEY `survey_obj_id` (`survey_obj_id`(128)),
  UNIQUE KEY `survey_obj_id_2` (`survey_obj_id`(128)),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Catalog Database' AUTO_INCREMENT=1047324 ;

-- --------------------------------------------------------

--
-- Table structure for table `obj_mapping`
--

DROP TABLE IF EXISTS `obj_mapping`;
CREATE TABLE IF NOT EXISTS `obj_mapping` (
  `obj_mapping_id` bigint(20) unsigned NOT NULL auto_increment,
  `obj_src_id` bigint(20) unsigned NOT NULL,
  `obj_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`obj_mapping_id`),
  KEY `fk_obj_src_id` (`obj_src_id`),
  KEY `fk_obj_tar_id` (`obj_tar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Catalog Database' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Qualitative`
--

DROP TABLE IF EXISTS `Qualitative`;
CREATE TABLE IF NOT EXISTS `Qualitative` (
  `pql_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `intensity` double default NULL,
  `ts` timestamp NULL default NULL,
  `pqt_id_left` bigint(20) unsigned NOT NULL,
  `pqt_id_right` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`pql_id`),
  KEY `fk_pqt_id_left` (`pqt_id_left`),
  KEY `fk_pqt_id_right` (`pqt_id_right`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Quantitative`
--

DROP TABLE IF EXISTS `Quantitative`;
CREATE TABLE IF NOT EXISTS `Quantitative` (
  `pqt_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `predicate` varchar(45) default NULL,
  `intensity` double default NULL,
  `ts` timestamp NULL default NULL,
  PRIMARY KEY  (`pqt_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Stores qualitative preferences' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `Quantitative_has_Attributes`
--

DROP TABLE IF EXISTS `Quantitative_has_Attributes`;
CREATE TABLE IF NOT EXISTS `Quantitative_has_Attributes` (
  `pqt_id` bigint(20) unsigned NOT NULL,
  `att_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`pqt_id`,`att_id`),
  KEY `att_id` (`att_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Quantitative_has_Tables`
--

DROP TABLE IF EXISTS `Quantitative_has_Tables`;
CREATE TABLE IF NOT EXISTS `Quantitative_has_Tables` (
  `pqt_id` bigint(20) unsigned NOT NULL,
  `table_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`pqt_id`,`table_id`),
  KEY `fk_table_id` (`table_id`),
  KEY `fk_pqt_id` (`pqt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `query_history`
--

DROP TABLE IF EXISTS `query_history`;
CREATE TABLE IF NOT EXISTS `query_history` (
  `query_his_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `survey_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `querys` text collate utf8_unicode_ci NOT NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`query_his_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=662 ;

-- --------------------------------------------------------

--
-- Table structure for table `result_history`
--

DROP TABLE IF EXISTS `result_history`;
CREATE TABLE IF NOT EXISTS `result_history` (
  `result_his_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `result_type` varchar(10) collate utf8_unicode_ci NOT NULL,
  `result_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `result_comment` varchar(50) collate utf8_unicode_ci NOT NULL,
  `result_size` bigint(20) unsigned NOT NULL,
  `result_content` mediumblob NOT NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`result_his_id`),
  KEY `fk_user_id` (`user_id`),
  KEY `result_name` (`result_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `set_contain_obj`
--

DROP TABLE IF EXISTS `set_contain_obj`;
CREATE TABLE IF NOT EXISTS `set_contain_obj` (
  `set_contain_obj_id` bigint(20) unsigned NOT NULL auto_increment,
  `set_src_id` bigint(20) unsigned NOT NULL,
  `object_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`set_contain_obj_id`),
  KEY `fk_set_src_id` (`set_src_id`),
  KEY `fk_object_tar_id` (`object_tar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Catalog Database' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `set_info`
--

DROP TABLE IF EXISTS `set_info`;
CREATE TABLE IF NOT EXISTS `set_info` (
  `set_id` bigint(20) unsigned NOT NULL auto_increment,
  `set_name` varchar(45) collate utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`set_id`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Catalog Database' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_contains`
--

DROP TABLE IF EXISTS `SN_contains`;
CREATE TABLE IF NOT EXISTS `SN_contains` (
  `contain_id` int(10) unsigned NOT NULL auto_increment,
  `contain_list_id` int(10) unsigned NOT NULL,
  `contain_unique_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`contain_id`),
  KEY `contain_list_id` (`contain_list_id`),
  KEY `contain_unique_id` (`contain_unique_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=542 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_experiment`
--

DROP TABLE IF EXISTS `SN_experiment`;
CREATE TABLE IF NOT EXISTS `SN_experiment` (
  `exp_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `exp_name` varchar(256) collate utf8_unicode_ci NOT NULL,
  `num_nights` int(10) NOT NULL,
  PRIMARY KEY  (`exp_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=155 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_exp_logs`
--

DROP TABLE IF EXISTS `SN_exp_logs`;
CREATE TABLE IF NOT EXISTS `SN_exp_logs` (
  `log_id` int(11) unsigned NOT NULL auto_increment,
  `exp_id` bigint(20) unsigned NOT NULL,
  `obj_list_id` int(11) unsigned NOT NULL,
  `obs_date` date default NULL,
  `unique_id` bigint(20) unsigned default NULL,
  `Number` varchar(45) collate utf8_unicode_ci default NULL,
  `Observation` varchar(100) collate utf8_unicode_ci default NULL,
  `int_value` int(11) default NULL,
  `time` varchar(45) collate utf8_unicode_ci default NULL,
  `filter` varchar(45) collate utf8_unicode_ci default NULL,
  `airmass` varchar(45) collate utf8_unicode_ci default NULL,
  `seeing` varchar(45) collate utf8_unicode_ci default NULL,
  `Notes` varchar(45) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1358 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_exp_log_matches`
--

DROP TABLE IF EXISTS `SN_exp_log_matches`;
CREATE TABLE IF NOT EXISTS `SN_exp_log_matches` (
  `log_id` int(11) unsigned NOT NULL,
  `file_number` int(11) unsigned NOT NULL,
  `file_name` varchar(100) collate utf8_unicode_ci default NULL,
  `flag` tinyint(4) default '0' COMMENT 'flag states if the image file has been collected yet (from remote sever to local server).',
  `collect_date` date default NULL,
  PRIMARY KEY  (`log_id`,`file_number`),
  KEY `fk_SN_exp_log_matches_3` (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SN_exp_log_source`
--

DROP TABLE IF EXISTS `SN_exp_log_source`;
CREATE TABLE IF NOT EXISTS `SN_exp_log_source` (
  `source_id` int(11) NOT NULL auto_increment,
  `exp_id` bigint(20) unsigned NOT NULL,
  `obj_list_id` int(10) unsigned NOT NULL,
  `obs_date` date NOT NULL,
  `log_content` mediumtext collate utf8_unicode_ci,
  `url` varchar(100) collate utf8_unicode_ci default NULL,
  `import_date` date default NULL,
  PRIMARY KEY  (`source_id`),
  KEY `fk_SN_exp_log_source_1` (`exp_id`),
  KEY `fk_SN_exp_log_source_2` (`obj_list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=85 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_exp_nights`
--

DROP TABLE IF EXISTS `SN_exp_nights`;
CREATE TABLE IF NOT EXISTS `SN_exp_nights` (
  `exp_id` bigint(20) unsigned NOT NULL,
  `exp_night` date NOT NULL,
  `num_hours` int(5) NOT NULL,
  `half_ab` char(1) collate utf8_unicode_ci NOT NULL,
  KEY `exp_id` (`exp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SN_exp_notes`
--

DROP TABLE IF EXISTS `SN_exp_notes`;
CREATE TABLE IF NOT EXISTS `SN_exp_notes` (
  `note_id` bigint(20) unsigned NOT NULL auto_increment,
  `exp_id` bigint(20) unsigned NOT NULL,
  `obj_list_id` int(10) unsigned NOT NULL,
  `unique_id` bigint(20) unsigned default NULL,
  `obs_date` date default NULL,
  `note` mediumtext collate utf8_unicode_ci,
  PRIMARY KEY  (`note_id`),
  UNIQUE KEY `note_id_UNIQUE` (`note_id`),
  KEY `fk_SN_exp_notes_1` (`exp_id`),
  KEY `fk_SN_exp_notes_2` (`obj_list_id`),
  KEY `fk_SN_exp_notes_3` (`unique_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=88 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_exp_objects`
--

DROP TABLE IF EXISTS `SN_exp_objects`;
CREATE TABLE IF NOT EXISTS `SN_exp_objects` (
  `exp_id` bigint(20) unsigned NOT NULL,
  `unique_id` bigint(20) unsigned NOT NULL,
  `b_peak` date NOT NULL,
  `obs_gap` int(3) NOT NULL default '1',
  `num_obs_times` int(3) NOT NULL,
  `priority` double NOT NULL default '0.5',
  KEY `fk_unique_id` (`unique_id`),
  KEY `exp_id` (`exp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SN_exp_results`
--

DROP TABLE IF EXISTS `SN_exp_results`;
CREATE TABLE IF NOT EXISTS `SN_exp_results` (
  `exp_id` bigint(20) unsigned NOT NULL,
  `obj_list_id` int(11) unsigned NOT NULL,
  `unique_id` bigint(20) unsigned NOT NULL,
  `obs_date` date NOT NULL,
  `flag` enum('0','1','2') collate utf8_unicode_ci NOT NULL,
  `j_value` int(11) unsigned default NULL,
  `h_value` int(11) unsigned default NULL,
  `k_value` int(11) unsigned default NULL,
  `file_set` int(11) unsigned default NULL,
  `sDuration` int(11) unsigned default NULL,
  PRIMARY KEY  (`exp_id`,`obj_list_id`,`unique_id`,`obs_date`),
  UNIQUE KEY `file_set_UNIQUE` (`file_set`),
  KEY `fk_new_table_1` (`exp_id`),
  KEY `fk_new_table_2` (`obj_list_id`),
  KEY `fk_new_table_3` (`unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SN_feeds`
--

DROP TABLE IF EXISTS `SN_feeds`;
CREATE TABLE IF NOT EXISTS `SN_feeds` (
  `feed_id` int(10) unsigned NOT NULL auto_increment,
  `feed_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `feed_url` varchar(100) collate utf8_unicode_ci NOT NULL,
  `feed_description` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`feed_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_known_list`
--

DROP TABLE IF EXISTS `SN_known_list`;
CREATE TABLE IF NOT EXISTS `SN_known_list` (
  `sn_id` bigint(20) unsigned NOT NULL auto_increment,
  `sn_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `sn_host_galaxy` varchar(64) collate utf8_unicode_ci default NULL,
  `sn_date` timestamp NULL default NULL,
  `sn_ra` double default NULL,
  `sn_dec` double default NULL,
  `sn_ra_hmsdms` varchar(20) collate utf8_unicode_ci NOT NULL,
  `sn_dec_hmsdms` varchar(20) collate utf8_unicode_ci NOT NULL,
  `sn_type` varchar(20) collate utf8_unicode_ci default NULL,
  `sn_mag` float default NULL,
  `sn_phase` varchar(20) collate utf8_unicode_ci default NULL,
  `sn_redshift` float default NULL,
  `sn_discoverer` varchar(128) collate utf8_unicode_ci default NULL,
  `sn_instrument` varchar(256) collate utf8_unicode_ci default NULL,
  `sn_spectrum` date default NULL,
  `sn_notes` varchar(512) collate utf8_unicode_ci default NULL,
  `sn_timestamp` date NOT NULL,
  PRIMARY KEY  (`sn_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6304 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_known_list_match`
--

DROP TABLE IF EXISTS `SN_known_list_match`;
CREATE TABLE IF NOT EXISTS `SN_known_list_match` (
  `kl_match_id` bigint(20) unsigned NOT NULL auto_increment,
  `kl_match_unique_id` bigint(20) unsigned NOT NULL,
  `kl_match_sn_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`kl_match_id`),
  KEY `kl_match_unique_id` (`kl_match_unique_id`),
  KEY `kl_match_sn_id` (`kl_match_sn_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=28167 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_lists`
--

DROP TABLE IF EXISTS `SN_lists`;
CREATE TABLE IF NOT EXISTS `SN_lists` (
  `list_id` int(10) unsigned NOT NULL auto_increment,
  `list_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `list_description` varchar(200) collate utf8_unicode_ci NOT NULL,
  `list_owner` varchar(50) collate utf8_unicode_ci NOT NULL,
  `list_owner_id` bigint(20) unsigned NOT NULL,
  `list_create_ts` timestamp NULL default '0000-00-00 00:00:00',
  `list_update_ts` timestamp NULL default '0000-00-00 00:00:00',
  `list_delete_ts` timestamp NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`list_id`),
  KEY `list_owner_id` (`list_owner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=117 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_matches`
--

DROP TABLE IF EXISTS `SN_matches`;
CREATE TABLE IF NOT EXISTS `SN_matches` (
  `match_id` bigint(20) unsigned NOT NULL auto_increment,
  `match_unique_id` bigint(20) unsigned NOT NULL,
  `match_object_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`match_id`),
  KEY `match_unique_id` (`match_unique_id`),
  KEY `match_object_id` (`match_object_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12336 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_messages`
--

DROP TABLE IF EXISTS `SN_messages`;
CREATE TABLE IF NOT EXISTS `SN_messages` (
  `msg_id` bigint(20) unsigned NOT NULL auto_increment,
  `msg_identifier` varchar(100) collate utf8_unicode_ci NOT NULL,
  `msg_hashed` char(32) collate utf8_unicode_ci NOT NULL,
  `msg_feed_id` int(10) unsigned NOT NULL,
  `msg_type` enum('object','annotation','unclassified','failure','unmatch') collate utf8_unicode_ci NOT NULL,
  `msg_title` varchar(100) collate utf8_unicode_ci NOT NULL,
  `msg_link` varchar(100) collate utf8_unicode_ci NOT NULL,
  `msg_description` varchar(500) collate utf8_unicode_ci NOT NULL,
  `msg_blob` blob NOT NULL,
  `msg_update_ts` timestamp NOT NULL default '0000-00-00 00:00:00',
  `msg_start_ts` timestamp NOT NULL default '0000-00-00 00:00:00',
  `msg_end_ts` timestamp NULL default NULL,
  PRIMARY KEY  (`msg_id`),
  KEY `msg_identifier` (`msg_identifier`),
  KEY `msg_hashed` (`msg_hashed`),
  KEY `msg_feed_id` (`msg_feed_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13867 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_objects`
--

DROP TABLE IF EXISTS `SN_objects`;
CREATE TABLE IF NOT EXISTS `SN_objects` (
  `object_id` bigint(20) unsigned NOT NULL auto_increment,
  `object_ra` double NOT NULL,
  `object_dec` double NOT NULL,
  `object_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `object_msg_hashed` char(32) collate utf8_unicode_ci default NULL,
  `object_type` varchar(20) collate utf8_unicode_ci default NULL,
  `object_redshift` float default NULL,
  `object_disc_mag` float default NULL,
  `object_phase` varchar(20) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`object_id`),
  KEY `object_msg_hashed` (`object_msg_hashed`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12779 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_trains`
--

DROP TABLE IF EXISTS `SN_trains`;
CREATE TABLE IF NOT EXISTS `SN_trains` (
  `train_id` int(10) unsigned NOT NULL auto_increment,
  `train_owner_id` bigint(20) unsigned NOT NULL,
  `feedback` int(10) unsigned NOT NULL,
  `weight1` int(10) unsigned NOT NULL,
  `weight2` int(10) unsigned NOT NULL,
  `weight3` int(10) unsigned NOT NULL,
  `weight4` int(10) unsigned NOT NULL,
  `weight5` int(10) unsigned NOT NULL,
  `weight6` int(10) unsigned NOT NULL,
  `weight7` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`train_id`),
  KEY `train_owner_id` (`train_owner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `SN_uniques`
--

DROP TABLE IF EXISTS `SN_uniques`;
CREATE TABLE IF NOT EXISTS `SN_uniques` (
  `unique_id` bigint(20) unsigned NOT NULL auto_increment,
  `unique_ra` double NOT NULL,
  `unique_dec` double NOT NULL,
  `unique_ra_hmsdms` varchar(20) collate utf8_unicode_ci NOT NULL,
  `unique_dec_hmsdms` varchar(20) collate utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY  (`unique_id`),
  KEY `unique_ra` (`unique_ra`),
  KEY `unique_dec` (`unique_dec`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11651 ;

-- --------------------------------------------------------

--
-- Table structure for table `survey`
--

DROP TABLE IF EXISTS `survey`;
CREATE TABLE IF NOT EXISTS `survey` (
  `survey_id` bigint(20) unsigned NOT NULL auto_increment,
  `survey_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `survey_url` text collate utf8_unicode_ci,
  `survey_description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`survey_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Catalog Database' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `survey_access_control`
--

DROP TABLE IF EXISTS `survey_access_control`;
CREATE TABLE IF NOT EXISTS `survey_access_control` (
  `survey_access_id` int(10) unsigned NOT NULL auto_increment,
  `survey_id` int(10) unsigned default NULL,
  `create_access` int(11) NOT NULL,
  `modify_access` int(11) NOT NULL,
  `delete_access` int(11) NOT NULL,
  `view_access` int(11) NOT NULL,
  `operator_type` enum('user','group','all_users','public') collate utf8_unicode_ci NOT NULL,
  `operator_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`survey_access_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `TableInfo`
--

DROP TABLE IF EXISTS `TableInfo`;
CREATE TABLE IF NOT EXISTS `TableInfo` (
  `table_id` bigint(20) unsigned NOT NULL auto_increment,
  `table_name` varchar(45) default NULL,
  `table_alias` varchar(5) default NULL,
  PRIMARY KEY  (`table_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `Table_has_Attributes`
--

DROP TABLE IF EXISTS `Table_has_Attributes`;
CREATE TABLE IF NOT EXISTS `Table_has_Attributes` (
  `table_id` bigint(20) unsigned NOT NULL,
  `att_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`table_id`,`att_id`),
  KEY `att_id` (`att_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tag_to_anno`
--

DROP TABLE IF EXISTS `tag_to_anno`;
CREATE TABLE IF NOT EXISTS `tag_to_anno` (
  `tag_to_anno_id` bigint(20) unsigned NOT NULL auto_increment,
  `tag_src` varchar(20) collate utf8_unicode_ci NOT NULL,
  `anno_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`tag_to_anno_id`),
  KEY `tag_src` (`tag_src`),
  KEY `fk_anno_tar_id` (`anno_tar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Annotation Database' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` bigint(20) unsigned NOT NULL auto_increment,
  `fname` varchar(30) collate utf8_unicode_ci NOT NULL,
  `lname` varchar(30) collate utf8_unicode_ci NOT NULL,
  `username` varchar(20) collate utf8_unicode_ci NOT NULL,
  `email` varchar(128) collate utf8_unicode_ci NOT NULL,
  `password` varchar(41) collate utf8_unicode_ci NOT NULL,
  `url` varchar(256) collate utf8_unicode_ci default NULL,
  `affiliation` varchar(45) collate utf8_unicode_ci default NULL,
  `role_id` int(10) NOT NULL default '2',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username_unique` (`username`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Database' AUTO_INCREMENT=129 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_access_control`
--

DROP TABLE IF EXISTS `user_access_control`;
CREATE TABLE IF NOT EXISTS `user_access_control` (
  `user_access_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `create_access` int(11) NOT NULL default '0',
  `modify_access` int(11) NOT NULL default '0',
  `delete_access` int(11) NOT NULL default '0',
  `view_access` int(11) NOT NULL default '0',
  `operator_type` enum('user','group','public','all_users') collate utf8_unicode_ci NOT NULL,
  `operator_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`user_access_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Defines user access rights' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_belong_group`
--

DROP TABLE IF EXISTS `user_belong_group`;
CREATE TABLE IF NOT EXISTS `user_belong_group` (
  `user_belong_group_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_src_id` bigint(20) unsigned NOT NULL,
  `group_tar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`user_belong_group_id`),
  KEY `fk_user_src_id` (`user_src_id`),
  KEY `fk_group_tar_id` (`group_tar_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Database' AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_email_confirmation`
--

DROP TABLE IF EXISTS `user_email_confirmation`;
CREATE TABLE IF NOT EXISTS `user_email_confirmation` (
  `confirmation_id` bigint(20) unsigned NOT NULL auto_increment,
  `activation_key` varchar(41) collate utf8_unicode_ci NOT NULL,
  `time_created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `fname` varchar(30) collate utf8_unicode_ci NOT NULL,
  `lname` varchar(30) collate utf8_unicode_ci NOT NULL,
  `username` varchar(20) collate utf8_unicode_ci NOT NULL,
  `email` varchar(128) collate utf8_unicode_ci NOT NULL,
  `password` varchar(41) collate utf8_unicode_ci NOT NULL,
  `url` varchar(256) collate utf8_unicode_ci default NULL,
  `affiliation` varchar(45) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`confirmation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

DROP TABLE IF EXISTS `user_login`;
CREATE TABLE IF NOT EXISTS `user_login` (
  `user_id` bigint(20) unsigned NOT NULL,
  `last_login` datetime default NULL,
  `last_logout` datetime default NULL,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `role_id` int(10) NOT NULL,
  `role_name` varchar(10) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_to_group`
--

DROP TABLE IF EXISTS `user_to_group`;
CREATE TABLE IF NOT EXISTS `user_to_group` (
  `user_id` bigint(20) unsigned NOT NULL,
  `group_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`group_id`),
  KEY `user_to_group_ibfk_2` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annotation`
--
ALTER TABLE `annotation`
  ADD CONSTRAINT `annotation_ibfk_2` FOREIGN KEY (`anno_type_id`) REFERENCES `anno_type` (`anno_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `annotation_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `anno_for_group`
--
ALTER TABLE `anno_for_group`
  ADD CONSTRAINT `anno_for_group_ibfk_10` FOREIGN KEY (`group_tar_id`) REFERENCES `group_info` (`group_id`),
  ADD CONSTRAINT `anno_for_group_ibfk_9` FOREIGN KEY (`anno_src_id`) REFERENCES `annotation` (`anno_id`);

--
-- Constraints for table `anno_for_user`
--
ALTER TABLE `anno_for_user`
  ADD CONSTRAINT `anno_for_user_ibfk_3` FOREIGN KEY (`anno_src_id`) REFERENCES `annotation` (`anno_id`),
  ADD CONSTRAINT `anno_for_user_ibfk_4` FOREIGN KEY (`user_tar_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `anno_to_anno`
--
ALTER TABLE `anno_to_anno`
  ADD CONSTRAINT `anno_to_anno_ibfk_3` FOREIGN KEY (`anno_src_id`) REFERENCES `annotation` (`anno_id`),
  ADD CONSTRAINT `anno_to_anno_ibfk_4` FOREIGN KEY (`anno_tar_id`) REFERENCES `annotation` (`anno_id`);

--
-- Constraints for table `anno_to_area_point`
--
ALTER TABLE `anno_to_area_point`
  ADD CONSTRAINT `anno_to_area_point_ibfk_1` FOREIGN KEY (`anno_src_id`) REFERENCES `annotation` (`anno_id`);

--
-- Constraints for table `anno_to_obj`
--
ALTER TABLE `anno_to_obj`
  ADD CONSTRAINT `anno_to_obj_ibfk_5` FOREIGN KEY (`anno_src_id`) REFERENCES `annotation` (`anno_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `anno_to_obj_ibfk_6` FOREIGN KEY (`obj_tar_id`) REFERENCES `object_info` (`object_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `anno_to_set`
--
ALTER TABLE `anno_to_set`
  ADD CONSTRAINT `anno_to_set_ibfk_3` FOREIGN KEY (`anno_src_id`) REFERENCES `annotation` (`anno_id`),
  ADD CONSTRAINT `anno_to_set_ibfk_4` FOREIGN KEY (`set_tar_id`) REFERENCES `set_info` (`set_id`);

--
-- Constraints for table `anno_to_view`
--
ALTER TABLE `anno_to_view`
  ADD CONSTRAINT `anno_to_view_ibfk_1` FOREIGN KEY (`anno_src_id`) REFERENCES `annotation` (`anno_id`);

--
-- Constraints for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `bookmark_of_anno`
--
ALTER TABLE `bookmark_of_anno`
  ADD CONSTRAINT `bookmark_of_anno_ibfk_1` FOREIGN KEY (`bookmark_id`) REFERENCES `bookmark` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookmark_of_anno_ibfk_2` FOREIGN KEY (`anno_id`) REFERENCES `annotation` (`anno_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookmark_of_loc`
--
ALTER TABLE `bookmark_of_loc`
  ADD CONSTRAINT `anno_of_loc_ibfk_1` FOREIGN KEY (`bookmark_id`) REFERENCES `bookmark` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookmark_of_obj`
--
ALTER TABLE `bookmark_of_obj`
  ADD CONSTRAINT `bookmark_of_obj_ibfk_1` FOREIGN KEY (`bookmark_id`) REFERENCES `bookmark` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookmark_of_obj_ibfk_2` FOREIGN KEY (`obj_id`) REFERENCES `object_info` (`object_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_in_group`
--
ALTER TABLE `group_in_group`
  ADD CONSTRAINT `group_in_group_ibfk_3` FOREIGN KEY (`group_src_id`) REFERENCES `group_info` (`group_id`),
  ADD CONSTRAINT `group_in_group_ibfk_4` FOREIGN KEY (`group_tar_id`) REFERENCES `group_info` (`group_id`);

--
-- Constraints for table `liveinterest`
--
ALTER TABLE `liveinterest`
  ADD CONSTRAINT `liveinterest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification_to_liveinterest`
--
ALTER TABLE `notification_to_liveinterest`
  ADD CONSTRAINT `notification_to_liveinterest_ibfk_1` FOREIGN KEY (`interest_id`) REFERENCES `liveinterest` (`interest_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notification_to_liveinterest_ibfk_2` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `object_info`
--
ALTER TABLE `object_info`
  ADD CONSTRAINT `object_info_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`);

--
-- Constraints for table `obj_mapping`
--
ALTER TABLE `obj_mapping`
  ADD CONSTRAINT `obj_mapping_ibfk_1` FOREIGN KEY (`obj_src_id`) REFERENCES `object_info` (`object_id`),
  ADD CONSTRAINT `obj_mapping_ibfk_2` FOREIGN KEY (`obj_tar_id`) REFERENCES `object_info` (`object_id`);

--
-- Constraints for table `Qualitative`
--
ALTER TABLE `Qualitative`
  ADD CONSTRAINT `Qualitative_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `Qualitative_ibfk_2` FOREIGN KEY (`pqt_id_left`) REFERENCES `Quantitative` (`pqt_id`),
  ADD CONSTRAINT `Qualitative_ibfk_3` FOREIGN KEY (`pqt_id_right`) REFERENCES `Quantitative` (`pqt_id`);

--
-- Constraints for table `Quantitative`
--
ALTER TABLE `Quantitative`
  ADD CONSTRAINT `Quantitative_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `Quantitative_has_Attributes`
--
ALTER TABLE `Quantitative_has_Attributes`
  ADD CONSTRAINT `Quantitative_has_Attributes_ibfk_1` FOREIGN KEY (`pqt_id`) REFERENCES `Quantitative` (`pqt_id`),
  ADD CONSTRAINT `Quantitative_has_Attributes_ibfk_2` FOREIGN KEY (`att_id`) REFERENCES `Attribute` (`att_id`);

--
-- Constraints for table `Quantitative_has_Tables`
--
ALTER TABLE `Quantitative_has_Tables`
  ADD CONSTRAINT `Quantitative_has_Tables_ibfk_1` FOREIGN KEY (`pqt_id`) REFERENCES `Quantitative` (`pqt_id`),
  ADD CONSTRAINT `Quantitative_has_Tables_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `TableInfo` (`table_id`);

--
-- Constraints for table `query_history`
--
ALTER TABLE `query_history`
  ADD CONSTRAINT `query_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `result_history`
--
ALTER TABLE `result_history`
  ADD CONSTRAINT `result_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `set_contain_obj`
--
ALTER TABLE `set_contain_obj`
  ADD CONSTRAINT `set_contain_obj_ibfk_1` FOREIGN KEY (`set_src_id`) REFERENCES `set_info` (`set_id`),
  ADD CONSTRAINT `set_contain_obj_ibfk_2` FOREIGN KEY (`object_tar_id`) REFERENCES `object_info` (`object_id`);

--
-- Constraints for table `set_info`
--
ALTER TABLE `set_info`
  ADD CONSTRAINT `set_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `SN_contains`
--
ALTER TABLE `SN_contains`
  ADD CONSTRAINT `SN_contains_ibfk_1` FOREIGN KEY (`contain_list_id`) REFERENCES `SN_lists` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `SN_contains_ibfk_2` FOREIGN KEY (`contain_unique_id`) REFERENCES `SN_uniques` (`unique_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_experiment`
--
ALTER TABLE `SN_experiment`
  ADD CONSTRAINT `SN_experiment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_exp_log_matches`
--
ALTER TABLE `SN_exp_log_matches`
  ADD CONSTRAINT `fk_SN_exp_log_matches_3` FOREIGN KEY (`log_id`) REFERENCES `SN_exp_logs` (`log_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_exp_log_source`
--
ALTER TABLE `SN_exp_log_source`
  ADD CONSTRAINT `fk_SN_exp_log_source_1` FOREIGN KEY (`exp_id`) REFERENCES `SN_experiment` (`exp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_SN_exp_log_source_2` FOREIGN KEY (`obj_list_id`) REFERENCES `SN_lists` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_exp_nights`
--
ALTER TABLE `SN_exp_nights`
  ADD CONSTRAINT `SN_exp_nights_ibfk_1` FOREIGN KEY (`exp_id`) REFERENCES `SN_experiment` (`exp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_exp_notes`
--
ALTER TABLE `SN_exp_notes`
  ADD CONSTRAINT `fk_SN_exp_notes_1` FOREIGN KEY (`exp_id`) REFERENCES `SN_experiment` (`exp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_SN_exp_notes_2` FOREIGN KEY (`obj_list_id`) REFERENCES `SN_lists` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_SN_exp_notes_3` FOREIGN KEY (`unique_id`) REFERENCES `SN_uniques` (`unique_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_exp_objects`
--
ALTER TABLE `SN_exp_objects`
  ADD CONSTRAINT `SN_exp_objects_ibfk_1` FOREIGN KEY (`exp_id`) REFERENCES `SN_experiment` (`exp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `SN_exp_objects_ibfk_2` FOREIGN KEY (`unique_id`) REFERENCES `SN_uniques` (`unique_id`);

--
-- Constraints for table `SN_exp_results`
--
ALTER TABLE `SN_exp_results`
  ADD CONSTRAINT `fk_new_table_1` FOREIGN KEY (`exp_id`) REFERENCES `SN_experiment` (`exp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_new_table_2` FOREIGN KEY (`obj_list_id`) REFERENCES `SN_lists` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_new_table_3` FOREIGN KEY (`unique_id`) REFERENCES `SN_uniques` (`unique_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_known_list_match`
--
ALTER TABLE `SN_known_list_match`
  ADD CONSTRAINT `FK_sn_list` FOREIGN KEY (`kl_match_sn_id`) REFERENCES `SN_known_list` (`sn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_unique` FOREIGN KEY (`kl_match_unique_id`) REFERENCES `SN_uniques` (`unique_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_lists`
--
ALTER TABLE `SN_lists`
  ADD CONSTRAINT `SN_lists_ibfk_1` FOREIGN KEY (`list_owner_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_matches`
--
ALTER TABLE `SN_matches`
  ADD CONSTRAINT `SN_matches_ibfk_1` FOREIGN KEY (`match_unique_id`) REFERENCES `SN_uniques` (`unique_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `SN_matches_ibfk_2` FOREIGN KEY (`match_object_id`) REFERENCES `SN_objects` (`object_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_messages`
--
ALTER TABLE `SN_messages`
  ADD CONSTRAINT `SN_messages_ibfk_1` FOREIGN KEY (`msg_feed_id`) REFERENCES `SN_feeds` (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SN_trains`
--
ALTER TABLE `SN_trains`
  ADD CONSTRAINT `SN_trains_ibfk_1` FOREIGN KEY (`train_owner_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Table_has_Attributes`
--
ALTER TABLE `Table_has_Attributes`
  ADD CONSTRAINT `Table_has_Attributes_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `TableInfo` (`table_id`),
  ADD CONSTRAINT `Table_has_Attributes_ibfk_2` FOREIGN KEY (`att_id`) REFERENCES `Attribute` (`att_id`);

--
-- Constraints for table `tag_to_anno`
--
ALTER TABLE `tag_to_anno`
  ADD CONSTRAINT `tag_to_anno_ibfk_1` FOREIGN KEY (`anno_tar_id`) REFERENCES `annotation` (`anno_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `role_id_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`role_id`);

--
-- Constraints for table `user_belong_group`
--
ALTER TABLE `user_belong_group`
  ADD CONSTRAINT `user_belong_group_ibfk_1` FOREIGN KEY (`user_src_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `user_belong_group_ibfk_2` FOREIGN KEY (`group_tar_id`) REFERENCES `group_info` (`group_id`);

--
-- Constraints for table `user_login`
--
ALTER TABLE `user_login`
  ADD CONSTRAINT `user_login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_to_group`
--
ALTER TABLE `user_to_group`
  ADD CONSTRAINT `user_to_group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_to_group_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `group_info` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
