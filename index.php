<?php
/*
Plugin Name: BlogAthlet's Taskmanager
Description: Aufgabenverwaltung im Blog
Author: Norman Merten
Author URI: http://blogathlet.de
Plugin URI: http://blogathlet.de
Version: 1.0
*/

/************************************DB Installation***********************************
*
*	Version: 1.0 
*	Zuletzt bearbeitet:
*	02.05.2014	
*/

//Hier funktioniert nun die Installation der Tabelle ;-)
/* Generelle install_db Funktion*/
function task_ba_db_install() {
	global $wpdb;
	$table_name_categorie = $wpdb->prefix . "task_ba_categorie";

	$task_ba_install_sql_categorie = "CREATE TABLE ".$table_name_categorie." (
	  		`id` bigint(20) unsigned NOT NULL auto_increment,
			  `name` varchar(255) NOT NULL default '',
			  `description` varchar(1000) NOT NULL default '',
			  `nn` varchar(255) NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  KEY `name` (`name`),
			  KEY `description` (`description`),
			  KEY `nn` (`nn`)
			);";
			
	$table_name_connect = $wpdb->prefix . "task_ba_connect";		
	$task_ba_install_sql_connect = "CREATE TABLE ".$table_name_connect." (
	  		`id` bigint(20) unsigned NOT NULL auto_increment,
			  `categorie` varchar(255) NOT NULL default '',
			  `task` varchar(255) NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  KEY `categorie` (`categorie`),
			  KEY `task` (`task`)
			);";

	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";			
	$runme_install_sql_tasks = "CREATE TABLE ".$table_name_tasks." (
	  		`id` bigint(20) unsigned NOT NULL auto_increment,
			  `name` varchar(255) NOT NULL default '0000-00-00',
			  `description` varchar(1000) NOT NULL default '',
			  `remind_date` varchar(255) NOT NULL default '', 
			  `dead_line` varchar(255) NOT NULL default '',
			  `status` varchar(255) NOT NULL default '',
			  `author_id` varchar(255) NOT NULL default '',
			  `delegated_user` varchar(255) NOT NULL default '',
			  `last_activity` varchar(255) NOT NULL default '',
			  `current_user` varchar(255) NOT NULL default '',
			  `last_notice_mail` varchar(255) NOT NULL default '',
			  `nn` varchar(255) NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  KEY `name` (`name`),
			  KEY `description` (`description`),
			  KEY `remind_date` (`remind_date`),
			  KEY `dead_line` (`dead_line`),
			  KEY `status` (`status`),
			  KEY `author_id` (`author_id`),
			  KEY `delegated_user` (`delegated_user`),
			  KEY `last_activity` (`last_activity`),
			  KEY `current_user` (`current_user`),
			  KEY `last_notice_mail` (`last_notice_mail`),
			  KEY `nn` (`nn`)
			);";


			
	/*Einbinden von Upgrade*/			
	require_once(ABSPATH. 'wp-admin/includes/upgrade.php');
	/*Datenbankverbindung*/
	dbDelta($task_ba_install_sql_categorie);
	dbDelta($task_ba_install_sql_connect);
	dbDelta($runme_install_sql_tasks);


	/* USER-LEVEL mit Berechtigungen
 	all = Display all user
	1 = subscriber
 	2 = editor
 	3 = author
 	7 = publisher
	10 = administrator
	*/

	/* Install Options */
	/* Who can see Grouptasks*/
	add_option( 'task_ba_grouptasks', '10', '', 'no' );
	/* Who can delegate Tasks*/
	add_option( 'task_ba_delegate', '10', '', 'no' );
	/* When should we sent the e-mail-notification for open tasks*/
	add_option( 'task_ba_noti_time_sec', '12', '', 'no' );
	
	/* When should we sent the e-mail-notification for open tasks*/
	add_option( 'task_ba_email_adress', '', '', 'no' );
	/* When should we sent the e-mail-notification for open tasks*/
	add_option( 'task_ba_email_connection_type', '', '', 'no' );
	/* When should we sent the e-mail-notification for open tasks*/
	add_option( 'task_ba_email_username', '', '', 'no' );
	/* When should we sent the e-mail-notification for open tasks*/
	add_option( 'task_ba_email_password', '', '', 'no' );
	/* When should we sent the e-mail-notification for open tasks*/
	add_option( 'task_ba_email_port', '', '', 'no' );
	/* When should we sent the e-mail-notification for open tasks*/
	add_option( 'task_ba_email_connection_security', '', '', 'no' );


}

function task_ba_db_drop()
	{
		/* Global */
		global $wpdb;
		
		/* Name + Remove Table task_ba_categorie */
		$table_name = $wpdb->prefix . "task_ba_categorie";
		$wpdb->query("DROP TABLE IF EXISTS ".$table_name."");
		/* Name + Remove Table task_ba_connect */
		$table_name = $wpdb->prefix . "task_ba_connect";
		$wpdb->query("DROP TABLE IF EXISTS ".$table_name."");
		/* Name + Remove Table task_ba_tasks */
		$table_name = $wpdb->prefix . "task_ba_tasks";
		$wpdb->query("DROP TABLE IF EXISTS ".$table_name."");

	}




//Fehler werden versteckt!!
//$wpdb->hide_errors();
error_reporting(0);

register_activation_hook(__FILE__, 'task_ba_db_install');

register_uninstall_hook(__FILE__, 'task_ba_db_drop');
include_once(dirname(__FILE__).'/inc/functions.php');
/* Funktion zur Ermittlung der Kategorien */





// Register style sheet.
add_action( 'admin_enqueue_scripts', 'register_plugin_styles' );

/**
 * Register style sheet.
 */
function register_plugin_styles() {
	wp_register_style( 'task-ba-style', plugins_url( 'task_ba_taskmanager/css/task_ba.css' ) );
	wp_enqueue_style( 'task-ba-style' );
}













/***** GLOBAL SETTINGS *******/
$logdata = plugin_dir_path( __FILE__ ).'log/task_log.log';
global $logdata;


/******* Include ********
*	include Modules
*
*
*/


/*	Die derzeitigen Funktionen sind: Aufgaben erstellen, editieren und markieren sowie delegieren und Gruppenaufgaben anlegen.
 * 									Einstellungen ändern, Log-Datei erstellen, Erinnerungen per E-Mail
 * 
 */

// FUNCTIONS, IMPORTANT TO BE FIRST
include_once('inc/functions.php');

// OPTION EDIT PAGE
include_once('inc/task.edit-page.php');

// OPTION EDIT PAGE
include_once('inc/options.page.php');

// CATEGORY EDIT PAGE
include_once('inc/category.edit-page.php');

// SHOW LOG-PAGE 
include_once('inc/log.page.php');

// SHOW DASHBOARD WIDGET
include_once('inc/widget.dashboard.open-task.php');

// DEBAN USER IF OPEN TASK
include_once('inc/task.deban-user.php');

// THE CONFIG CRON FOR MAIL-FUNCTION
include_once('inc/mail.config-cron.php');

// REMEMBER IF MAIL IS NOT COMPLETED
include_once('inc/mail.remind-task.php');

// WARNING IF MAIL IS NOT COMPLETED
include_once('inc/mail.warn-task.php');









