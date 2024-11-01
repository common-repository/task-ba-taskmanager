<?php



/*******************************************DASHBOARD WIDGET ****************************************
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function task_todo_dashboard_widgets() {

	wp_add_dashboard_widget(
                 'tasks_todo',         // Widget slug.
                 'Task Todo Next Widget',         // Title.
                 'task_ba_widget_todo' // Display function.
        );	
}
add_action( 'wp_dashboard_setup', 'task_todo_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function task_ba_widget_todo() {



	/* Global */
	global $wpdb;

	$table_name_categorie = $wpdb->prefix . "task_ba_tasks";

	/* Wir erstellen hier den Table-Code für die Tabelle*/
	?>
	<h3>Deine Aufgaben</h3>
	<table>
	<tr>
	<th style="width:200px">Titel</th>
	<th style="width:200px">Beschreibung</th>
	<th style="width:100px">Erledigen bis</th>
	<th style="width:150px">Status</th>
	</tr>
	<?php
	$current_user = wp_get_current_user();
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_categorie." WHERE author_id = ".$current_user->ID." AND remind_date < ".time()." AND status NOT LIKE '9' ORDER BY dead_line DESC");
	foreach ( $selected_categorie as $selected_categorie_id ) {
		echo "<tr>";
		echo "<td>" . $selected_categorie_id->name ;
		echo "</td>";
		echo "<td>";
		if(strlen($selected_categorie_id->description)>50) echo substr($selected_categorie_id->description,0,50)."..."; 
		else echo $selected_categorie_id->description;  
		echo "</td>";
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->dead_line) . "</td>";
		echo '<td style="text-align:center;">' . return_status_named($selected_categorie_id->status) . "</td>";
		}

		
	echo "</table>";






} 

?>