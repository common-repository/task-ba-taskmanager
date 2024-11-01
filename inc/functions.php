<?php
/************************************GENERAL FUNCTIONS***********************************
*
*	Version 0.9
*	Zuletzt bearbeitet:
*	28.04.2014		
*/


function get_user_role($value){
	
	global $wpdb;
$user_role = $wpdb->get_row("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = ".$value." AND meta_key = '".$wpdb->prefix ."user_level'", ARRAY_N);
		
	if($user_role[0] != null){return $user_role[0];} else { return 'no user';}

}


function format_date_to_unix($value){


$date_us = explode('-', $value);
if(checkdate($date_us[1],$date_us[2],$date_us[0])) $value  = mktime(1, 0, 0, $date_us[1], $date_us[2], $date_us[0]);

$date = explode('.', $value);
if(checkdate($date[1],$date[0],$date[2])) $value  = mktime(1, 0, 0, $date[1], $date[0], $date[2]);
	
	return $value;
}

function format_unix_to_date($value){
	return date("d.m.Y", $value);
}



//LOG-Datei schreiben
function writelogdata ($logdatei, $hinweis) {
	
	if(!file_exists(dirname($logdatei))){
		mkdir(dirname($logdatei));
	
	}  
	
	date_default_timezone_set(get_option('timezone_string'));
	
	$createlogfile = fopen($logdatei, "a");
	
	/*Der ¸bergeben Inhalt wird aufgenommen*/
	$content = "[".date("Y-m-d H:i:s")."] ".$hinweis."\r\n";
	
	/*Bearbeitung wird beendet*/
	fwrite($createlogfile, $content);
}


function return_status_named($value){
if($value == 0) return 'Noch nicht begonnen';
if($value == 1) return 'Begonnen';
if($value == 2) return 'In Bearbeitung';
if($value == 5) return 'Erinnert';
if($value == 7) return 'Angemahnt';
if($value == 9) return 'Beendet';
}



function get_user_nicename($value){
	
	if($value == 'all') return 'alle';
	
	global $wpdb;

	$table_user = $wpdb->users;

	
	$selected_categorie = $wpdb->get_row("SELECT * FROM ".$table_user." WHERE ID = ".$value."");
	if(!empty($selected_categorie)){
	
	return $selected_categorie->user_nicename;
	}else{
	return false;
	}


}

function get_user_email($value){
	global $wpdb;

	$table_user = $wpdb->users;

	
	$selected_categorie = $wpdb->get_row("SELECT * FROM ".$table_user." WHERE ID = ".$value."");
	if(!empty($selected_categorie)){
	
	return $selected_categorie->user_email;
	}else{
	return false;
	}


}


function get_user_id_email($value){
	global $wpdb;

	$table_user = $wpdb->users;

	
	$selected_categorie = $wpdb->get_row("SELECT * FROM ".$table_user." WHERE user_email = '".$value."'");
	if(!empty($selected_categorie)){
	
	return $selected_categorie->ID;
	}else{
	return false;
	}


}


function set_ban_for_users($task_ba_task_id){

	/* Global */
	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	
	$current_user = wp_get_current_user();
	
	$wpdb->update( $table_name_tasks, array( 'current_user' =>  $current_user->ID,
											 'last_activity' => time()
											  ),
											array( 'id' => $task_ba_task_id ) );


}


function delete_ban_for_users($task_ba_task_id){

	/* Global */
	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$wpdb->update( $table_name_tasks, array( 'current_user' =>  '',
											 'last_activity' => time()
											  ),
											array( 'id' => $task_ba_task_id ) );


}


function current_user_not_banned($value){
	global $wpdb;

	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";

	$current_user = wp_get_current_user();
	$selected_categorie = $wpdb->get_row("SELECT * FROM ".$table_name_tasks." WHERE id = ".$value."");
	if($selected_categorie->current_user != '' AND $selected_categorie->current_user != $current_user->ID){
	
	return false;
	
	} else {
	
	
	return true;}

}




function get_user_form_options(){
	
	global $wpdb;

	$table_user = $wpdb->users;

	$current_user = wp_get_current_user();
	
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_user." ORDER BY user_nicename ASC");
	
	echo '<option value="">nicht delegieren</option>';
	
	foreach ( $selected_categorie as $selected_categorie_id ) {
		if($selected_categorie_id->ID == $current_user->ID) continue;
		echo '<option value="'.$selected_categorie_id->ID.'"';
		if($_POST["task_ba_task_delegated_user"]==$selected_categorie_id->ID)echo ' selected ';
		echo '>';
		echo $selected_categorie_id->user_nicename;
		echo "</option>";

	
		}
		if( get_user_role($current_user->ID) >= get_option('task_ba_delegate')){
		echo '<option value="all"';
		if($_POST["task_ba_task_delegated_user"]=='all')echo ' selected ';
		echo '>';
		echo 'Gruppenaufgabe';
		echo "</option>";
		}


}



/***************** WP JAVASCRIPT ***********************
*
*
*
*/




function javascript_codes(){

echo '
<script type="text/javascript">
function show_hide (id_object) {
	if(document.getElementById(id_object).style.display != "none") {
		document.getElementById(id_object).style.display = "none";
	} else {
	document.getElementById(id_object).style.display = "inline-block";
	
	}



}
</script>


';





}

function javascript_code_change_tasks_listing(){

echo '

<script type="text/javascript">
function change_tasks_listing (id_object_one, id_object_two) {
	if(document.getElementById(id_object_one).style.display != "none") {
		document.getElementById(id_object_one).style.display = "none";
		document.getElementById(id_object_two).style.display = "inline-block";
	} else {
	document.getElementById(id_object_one).style.display = "inline-block";
		document.getElementById(id_object_two).style.display = "none";
	
	}



}
</script>
';





}


?>