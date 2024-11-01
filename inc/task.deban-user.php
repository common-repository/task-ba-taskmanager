<?php


// send automatic scheduled email
if ( !wp_next_scheduled('my_task_hook') ) {
	wp_schedule_event( time(), 'threetimes', 'my_task_hook' ); // hourly, daily and twicedaily
}

function cron_deban_user() {
	
	/*General-Functions*/
	global $logdata;
	
	
	/* Global */
	global $wpdb;

	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_tasks."");
	foreach ( $selected_categorie as $selected_categorie_id ) {
		if($selected_categorie_id->last_activity < (time()-180) AND $selected_categorie_id->current_user != ''){
		$wpdb->update( $table_name_tasks, array( 'current_user' =>  '',
											 'last_activity' => time()
											  ),
											array( 'id' => $selected_categorie_id->id ) );
											
		writelogdata($logdata, 'Task '.$selected_categorie_id->id.' for all reopend!');
		
		}
	}
	
	
	
	
	
}
add_action('my_task_hook', 'cron_deban_user');


?>