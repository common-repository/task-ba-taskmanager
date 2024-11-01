<?php



/************************************Log Page***********************************
*
*	Version 0.1
*	Zuletzt bearbeitet:
*	05.05.2014		
*
*
*/

// Die List-Page ist für das Anlegen von Listen-Kategorien zuständig


/*Schritt 2*/
add_action( 'admin_menu' , 'task_ba_log_page');

/*Schritt 1*/
function task_ba_log_page() {
add_submenu_page('task-ba-manage-tasks' , "Logbuch" , "Logbuch" , 'activate_plugins' , 'task-ba-see-log' , 'task_ba_see_log_page');

}

/*Schritt 3*/
function task_ba_see_log_page() {
if ( !current_user_can( 'manage_categories' ))
{
wp_die( _( 'Du hast nicht die entsprechende Berechtigung'));
}

/* Global */
global $wpdb;
global $logdata;
$chosen_sql_command = ' AND author_id = 1';
$chosen_sql_command = '';

	
	if(get_option('timezone_string') != '' ){
		date_default_timezone_set( get_option('timezone_string') );
	}
	
	$table_name_categorie = $wpdb->prefix . "task_ba_tasks";
	$categorie_available = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_categorie." WHERE name <> ''".$chosen_sql_command.""));


	echo '<br>';
	echo 'aktuelle Zeit: '.date("Y.m.d H:i:s").'<br>';



if(file_exists($logdata)) {
echo 'Datei vorhanden';
} else {
echo 'Datei nicht gefunden';
}



$anzahl = count(file($logdata));  
$zeile = file($logdata);




echo '<br>';
echo "<div>";
$userdatei = fopen($logdata, "r");
for($x = ($anzahl-1); $x >= 0; $x--)
   {
   echo $zeile[$x]."<br>";
   }
fclose($userdatei);
echo "</div>";


}




?>