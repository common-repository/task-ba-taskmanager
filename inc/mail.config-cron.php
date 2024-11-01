<?php

/************************************GENERAL FUNCTIONS***********************************
*
*	Version 1.0
*	Zuletzt bearbeitet:
*	04.05.2014		
*/


/*************************************
*
*	Hier erfolgt der Cronjobs mit dreiminütiger Ausführung	
*	zur Freigabe von bearbeiteten Aufgabem
*
*/


add_filter( 'cron_schedules', 'filter_cron_schedules_threetimes' );
// add custom time to cron
function filter_cron_schedules_threetimes( $schedules ) {
	
	$schedules['threetimes'] = array( 
		'interval' => 180, // seconds
		'display'  => __( '3 Minutes' ) 
	);
	
	return $schedules;
}

add_filter( 'cron_schedules', 'filter_cron_schedules_minutely' );
// add custom time to cron
function filter_cron_schedules_minutely( $schedules ) {
	
	$schedules['minutely'] = array( 
		'interval' => 60, // seconds
		'display'  => __( 'Every Minute' ) 
	);
	
	return $schedules;
}



// send automatic scheduled email
if ( !wp_next_scheduled('task_ba_hook_sent_mail') ) {
	wp_schedule_event( time(), 'minutely', 'task_ba_hook_sent_mail' ); // hourly, daily and twicedaily
}


?>