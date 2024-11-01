<?php



/************************************TASK BA Tasks***********************************
*
*	Version 1.0
*	Zuletzt bearbeitet:
*	22.04.2014		
*/

/*Schritt "*/
add_action( 'admin_menu' , 'task_ba_menu');

/*Schritt 1*/
function task_ba_menu() {




$current_user = wp_get_current_user();
global $wpdb;
$table_name_tasks = $wpdb->prefix . "task_ba_tasks";

$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (delegated_user = '".$current_user->ID."' OR author_id = '".$current_user->ID."' OR delegated_user = 'all') AND status NOT LIKE 9 AND remind_date < ".time()." ORDER BY dead_line DESC"));


add_menu_page('Task BA Taskmanager' , "Aufgaben <span class='update-plugins count-".$tasks_counted."' title='$warning_title'><span class='update-count'>".$tasks_counted."</span></span>" , 'read' , 'task-ba-manage-tasks' , 'show_main_page', plugins_url( 'task_ba_taskmanager/icon/task_icon_160px.png' ), 9);




}

function test() {
if ( !current_user_can( 'read' ))
{
wp_die( _( 'Du hast nicht die entsprechende Berechtigung'));
}
}



/*Schritt 3*/
function show_main_page(){
if ( !current_user_can( 'read' ))
{
wp_die( _( 'Du hast nicht die entsprechende Berechtigung'));
}



/*General-Functions*/
global $wpdb;
global $logdata;
$current_user = wp_get_current_user();



/* Wir erstellen die Funktion zur Erstellung einer neuen Kategorie */
function task_ba_create_task() {

/* Global */
global $wpdb;
global $logdata;
$table_name_categorie = $wpdb->prefix . "task_ba_tasks";

$current_user = wp_get_current_user();
$_POST["task_ba_task_description"] = htmlentities($_POST["task_ba_task_description"]);

$wpdb->insert( $table_name_categorie, array( 'name' => $_POST["task_ba_task_name"],
											 'description' => $_POST["task_ba_task_description"],
											 'remind_date' => format_date_to_unix($_POST["task_ba_task_remind_date"]),
											 'dead_line' => format_date_to_unix($_POST["task_ba_task_dead_line"]),
											 'status' => $_POST["task_ba_task_status"],
											 'author_id' => $_POST["task_ba_task_author"],
											 'delegated_user' => $_POST["task_ba_task_delegated_user"],
											 'last_activity' => time() 
											    ) );

}









function check_doubled_datas(){
	/* Global */
	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$task_ba_task_name = $_POST['task_ba_task_name'];
	$task_ba_task_description = $_POST['task_ba_task_description'];
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE name = %s AND description = %d", $task_ba_task_name, $task_ba_task_description));

	
	if($tasks_counted == 0){
	return true;
	} else {
	return false;
	
	}
	




}







function check_form_datas() {

if(empty($_POST["task_ba_task_name"])) $failure_remind_date .= 'Bitte gebe einen Titel ein<br>';

if(empty($_POST["task_ba_task_description"])) $failure_remind_date .= 'Bitte gebe eine Beschreibung ein<br>';


if (preg_match('#^[a-z]{5}$#i', $_POST["task_ba_task_remind_date"]))  $failure_remind_date .= 'Erinnerung am...: Du darfst nur Zahlen verwenden<br>';
if(empty($_POST["task_ba_task_remind_date"])) $failure_remind_date .= 'Erinnerung am...: Bitte gebe ein Datum ein<br>';
$date = explode('.', $_POST["task_ba_task_remind_date"]);
$date_us = explode('-', $_POST["task_ba_task_remind_date"]);
if( checkdate($date[1],$date[0],$date[2]) OR checkdate($date_us[2],$date_us[1],$date_us[0]) ) { echo ''; }else{ $failure_remind_date .= 'Erinnerung am...: Das ist kein gültiges Datum verwenden<br>'; }


if (preg_match('#^[a-z]{5}$#i', $_POST["task_ba_task_dead_line"])) $failure_dead_line .= 'Zu erledigen bis...: Du darfst nur Zahlen verwenden<br>';
if(empty($_POST["task_ba_task_dead_line"])) $failure_dead_line .= 'Zu erledigen bis...: Bitte gebe ein Datum ein<br>';
$date = explode('.', $_POST["task_ba_task_dead_line"]);
$date_us = explode('-', $_POST["task_ba_task_remind_date"]);
if( checkdate($date[1],$date[0],$date[2]) OR checkdate($date_us[2],$date_us[1],$date_us[0]) ) { echo ''; }else{ $failure_remind_date .= 'Zu erledigen bis...: Das ist kein gültiges Datum verwenden<br>'; }

if(format_date_to_unix($_POST["task_ba_task_remind_date"]) > format_date_to_unix($_POST["task_ba_task_dead_line"])) $failure_date .= 'Das Erledigungsdatum ist früher als das Erinnerungsdatum';



$notice_failure .= $failure_remind_date;
$notice_failure .= $failure_dead_line;
$notice_failure .= $failure_date;



if(empty($notice_failure)) { 
	return true; 
}else{
	return '<font color="red"> '.$notice_failure.'</font>';
}

}












function my_tasks_available(){

	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$current_user = wp_get_current_user();
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (author_id = ".$current_user->ID." OR delegated_user = ".$current_user->ID." OR delegated_user = 'all') AND status NOT LIKE '9'", $current_user->ID));
	if($tasks_counted > 0) return true;
	else return false;


}

function count_my_tasks_available(){

	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$current_user = wp_get_current_user();
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (author_id = ".$current_user->ID." OR delegated_user = ".$current_user->ID." OR delegated_user = 'all') AND status NOT LIKE '9'", $current_user->ID));
	if($tasks_counted > 0) return $tasks_counted;
	else return false;


}



function show_my_tasks(){

	/* Global */
	global $wpdb;

	$table_name_categorie = $wpdb->prefix . "task_ba_tasks";

	/* Wir erstellen hier den Table-Code für die Tabelle*/
	?>
	<h3>Deine fällige Aufgaben</h3>
	<table>
	<tr>
	<th style="width:50px; text-align:left;">ID</th>
	<th style="width:200px">Titel</th>
	<th style="width:100px">Erinnerung am</th>
	<th style="width:100px">Erledigen bis</th>
	<th style="width:150px">Status</th>
	</tr>
	<?php
	$current_user = wp_get_current_user();
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_categorie." WHERE author_id = ".$current_user->ID."  AND delegated_user NOT LIKE 'all' AND status NOT LIKE '9' ORDER BY dead_line DESC");
	foreach ( $selected_categorie as $selected_categorie_id ) {
		echo "<tr>";
		echo "<td>";
		echo $selected_categorie_id->id;
		echo "</td>";
		echo "<td>" . $selected_categorie_id->name ;
		if(!empty($selected_categorie_id->delegated_user)) {echo"<br>(deligiert an: ".get_user_nicename($selected_categorie_id->delegated_user).")</td>";}else{echo "</td>";}
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->remind_date) . "</td>";
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->dead_line) . "</td>";
		echo '<td style="text-align:center;">' . return_status_named($selected_categorie_id->status) . "</td>";
		
		if(current_user_not_banned($selected_categorie_id->id) == true){
		?>
		<td>
		<form name="edit_categorie" method="post">
		<input name="task_ba_task_id" type="hidden" value="<?php echo $selected_categorie_id->id; ?>" size="10">
		<input name="task_ba_task_name" type="hidden" value="<?php echo $selected_categorie_id->name; ?>" size="255">
		<input name="task_ba_task_description" type="hidden" value="<?php echo $selected_categorie_id->description; ?>" size="1000">
		<input name="task_ba_task_remind_date" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->remind_date); ?>" size="10">
		<input name="task_ba_task_dead_line" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->dead_line); ?>" size="10">
		<input name="task_ba_task_status" type="hidden" value="<?php echo $selected_categorie_id->status; ?>" size="2">
		<input name="task_ba_task_author" type="hidden" value="<?php echo $selected_categorie_id->author_id; ?>" size="2">
		<input name="task_ba_task_delegated_user" type="hidden" value="<?php echo $selected_categorie_id->delegated_user; ?>" size="2">
		<input type="submit" name="taks_ba_edit_task" value=" Bearbeiten  ">
		<input type="submit" name="taks_ba_delete_task" value=" Löschen  ">
		</form>
		</td>
		<?php
		
		
		}else {
		
		echo "<td>Gesperrt</td>";
		
		}
	}
	echo "</table>";
}


function delegated_tasks_available(){

	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$current_user = wp_get_current_user();
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE delegated_user = ".$current_user->ID." AND status NOT LIKE 9 ORDER BY dead_line DESC"));
	if($tasks_counted > 0) return true;
	else return false;


}


function show_delegated_tasks(){

	/* Global */
	global $wpdb;

	$table_name_categorie = $wpdb->prefix . "task_ba_tasks";

	/* Wir erstellen hier den Table-Code für die Tabelle*/
	?>
	<h3>Dir zugeteilte fällige Aufgaben</h3>
	<table>
	<tr>
	<th style="width:50px; text-align:left;">ID</th>
	<th style="width:200px">Titel</th>
	<th style="width:100px">Erinnerung am</th>
	<th style="width:100px">Erledigen bis</th>
	<th style="width:150px">Status</th>
	</tr>
	<?php
	$current_user = wp_get_current_user();
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_categorie." WHERE delegated_user = ".$current_user->ID." AND status NOT LIKE 9 ORDER BY dead_line DESC");
	foreach ( $selected_categorie as $selected_categorie_id ) {
		
		if($current_user->ID == $selected_categorie_id->author_id){$author_id = 'dir';}else{$author_id = get_user_nicename($selected_categorie_id->author_id);}
		
	
		echo "<tr>";
		echo "<td>";
		echo $selected_categorie_id->id;
		echo "</td>";
		echo "<td>" . $selected_categorie_id->name;
		if(!empty($selected_categorie_id->delegated_user)) {echo"<br>(erhalten von: ".$author_id.")</td>";}else{echo "</td>";}
		
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->remind_date) . "</td>";
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->dead_line) . "</td>";
		echo '<td style="text-align:center;">' . return_status_named($selected_categorie_id->status) . "</td>";
		if(current_user_not_banned($selected_categorie_id->id) == true){
		
		?>
		<td>
		<form name="edit_categorie" method="post">
		<input name="task_ba_task_id" type="hidden" value="<?php echo $selected_categorie_id->id; ?>" size="10">
		<input name="task_ba_task_name" type="hidden" value="<?php echo $selected_categorie_id->name; ?>" size="255">
		<input name="task_ba_task_description" type="hidden" value="<?php echo $selected_categorie_id->description; ?>" size="1000">
		<input name="task_ba_task_remind_date" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->remind_date); ?>" size="10">
		<input name="task_ba_task_dead_line" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->dead_line); ?>" size="10">
		<input name="task_ba_task_status" type="hidden" value="<?php echo $selected_categorie_id->status; ?>" size="2">
		<input name="task_ba_task_delegated_user" type="hidden" value="<?php echo $selected_categorie_id->delegated_user; ?>" size="2">
		<input name="task_ba_task_author" type="hidden" value="<?php echo $selected_categorie_id->author_id; ?>" size="2">
		<input type="submit" name="taks_ba_edit_task" value=" Bearbeiten  ">
		<input type="submit" name="taks_ba_delete_task" value=" Löschen  ">
		</form>
		</td>
		<?php
		}else {
		
		echo "<td>Gesperrt</td>";
		
		}
		}
		
	echo "</table>";
}





function count_all_tasks(){

	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	
	$current_user = wp_get_current_user();
	if($current_user->wp_user_level >= get_option('task_ba_grouptasks')) {
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (author_id = ".$current_user->ID." OR delegated_user = ".$current_user->ID." OR delegated_user = 'all') AND status NOT LIKE 9", $current_user->ID));
	}else if($current_user->wp_user_level < get_option('task_ba_grouptasks')) {
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (author_id = ".$current_user->ID." OR delegated_user = ".$current_user->ID.") AND status NOT LIKE 9", $current_user->ID));
	}
	
	if($tasks_counted > 0){
		return $tasks_counted;
	} else {
		return 0;
	}


}



function important_tasks_available(){

	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$current_user = wp_get_current_user();
	
	
	$current_user = wp_get_current_user();
	if($current_user->wp_user_level >= get_option('task_ba_grouptasks')) {
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (delegated_user = '".$current_user->ID."' OR author_id = '".$current_user->ID."' OR delegated_user = 'all') AND status NOT LIKE 9 AND remind_date < ".time()." ORDER BY dead_line DESC"));
	}else if($current_user->wp_user_level < get_option('task_ba_grouptasks')) {
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (delegated_user = '".$current_user->ID."' OR author_id = '".$current_user->ID."') AND status NOT LIKE 9 AND remind_date < ".time()." ORDER BY dead_line DESC"));
	}
	
	if($tasks_counted > 0){ return true;
	} else {
		return false;
	}


}


function count_important_tasks_available(){

	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	
	$current_user = wp_get_current_user();
	if($current_user->wp_user_level >= get_option('task_ba_grouptasks')) {
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (delegated_user = '".$current_user->ID."' OR author_id = '".$current_user->ID."' OR delegated_user = 'all') AND status NOT LIKE 9 AND remind_date < ".time()." ORDER BY dead_line DESC"));
	}else if($current_user->wp_user_level < get_option('task_ba_grouptasks')) {
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (delegated_user = '".$current_user->ID."' OR author_id = '".$current_user->ID."') AND status NOT LIKE 9 AND remind_date < ".time()." ORDER BY dead_line DESC"));
	}
	
	if($tasks_counted > 0){
		return $tasks_counted;
	} else {
		return 0;
	}


}


function show_important_tasks(){

	/* Global */
	global $wpdb;

	$table_name_categorie = $wpdb->prefix . "task_ba_tasks";

	/* Wir erstellen hier den Table-Code für die Tabelle*/
	?>
	<table>
	<tr>
	<th style="width:50px; text-align:left;">ID</th>
	<th style="width:200px">Titel</th>
	<th style="width:100px">Erinnerung am</th>
	<th style="width:100px">Erledigen bis</th>
	<th style="width:150px">Status</th>
	</tr>
	<?php
	$current_user = wp_get_current_user();
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_categorie." WHERE (delegated_user = '".$current_user->ID."' OR author_id = '".$current_user->ID."' OR delegated_user = 'all') AND status NOT LIKE 9 AND remind_date < ".time()." ORDER BY dead_line DESC");
	foreach ( $selected_categorie as $selected_categorie_id ) {
	
	if($current_user->wp_user_level < get_option('task_ba_grouptasks') AND $selected_categorie_id->delegated_user != $current_user->ID AND $selected_categorie_id->delegated_user == 'all' AND $selected_categorie_id->delegated_user == $current_user->ID) continue;
	
	if($current_user->ID == $selected_categorie_id->author_id){$author_id = 'dir';}else{$author_id = get_user_nicename($selected_categorie_id->author_id);}
		echo "<tr>";
		echo "<td>";
		echo $selected_categorie_id->id;
		echo "</td>";
		echo "<td>" . $selected_categorie_id->name;
		if(!empty($selected_categorie_id->delegated_user)) {echo"<br>(erhalten von: ".$author_id.")</td>";}else{echo "</td>";}
		
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->remind_date) . "</td>";
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->dead_line) . "</td>";
		echo '<td style="text-align:center;">' . return_status_named($selected_categorie_id->status) . "</td>";
		if(current_user_not_banned($selected_categorie_id->id) == true){
		
		?>
		<td>
		<form name="edit_categorie" method="post">
		<input name="task_ba_task_id" type="hidden" value="<?php echo $selected_categorie_id->id; ?>" size="10">
		<input name="task_ba_task_name" type="hidden" value="<?php echo $selected_categorie_id->name; ?>" size="255">
		<input name="task_ba_task_description" type="hidden" value="<?php echo $selected_categorie_id->description; ?>" size="1000">
		<input name="task_ba_task_remind_date" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->remind_date); ?>" size="10">
		<input name="task_ba_task_dead_line" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->dead_line); ?>" size="10">
		<input name="task_ba_task_status" type="hidden" value="<?php echo $selected_categorie_id->status; ?>" size="2">
		<input name="task_ba_task_delegated_user" type="hidden" value="<?php echo $selected_categorie_id->delegated_user; ?>" size="2">
		<input name="task_ba_task_author" type="hidden" value="<?php echo $selected_categorie_id->author_id; ?>" size="2">
		<input type="submit" name="taks_ba_edit_task" value=" Bearbeiten  ">
		<input type="submit" name="taks_ba_delete_task" value=" Löschen  ">
		</form>
		</td>
		<?php
		}else {
		
		echo "<td>Gesperrt</td>";
		
		}
		}
		
	echo "</table>";
}




function group_tasks_available(){

	global $wpdb;
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	
	$current_user = wp_get_current_user();
	
	if($current_user->wp_user_level >= get_option('task_ba_grouptasks')){
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE delegated_user = %s AND status NOT LIKE '9'" , 'all'));
	
	}else if($current_user->wp_user_level < get_option('task_ba_grouptasks')){
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE delegated_user = %s AND author_id = ".$current_user->ID." AND status NOT LIKE '9'" , 'all'));
	}
	
	
	if($tasks_counted > 0) return true;
	else return false;


}

function show_group_tasks(){

	/* Global */
	global $wpdb;

	$table_name_categorie = $wpdb->prefix . "task_ba_tasks";

	/* Wir erstellen hier den Table-Code für die Tabelle*/
	?>
	<h3>Gruppenaufgaben</h3>
	<table>
	<tr>
	<th style="width:50px; text-align:left;">ID</th>
	<th style="width:200px">Titel</th>
	<th style="width:100px">Erinnerung am</th>
	<th style="width:100px">Erledigen bis</th>
	<th style="width:150px">Status</th>
	</tr>
	<?php
	$current_user = wp_get_current_user();
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_categorie." WHERE delegated_user = 'all' AND status NOT LIKE '9' ORDER BY dead_line DESC");
	foreach ( $selected_categorie as $selected_categorie_id ) {
	if($current_user->wp_user_level < get_option('task_ba_grouptasks') AND $selected_categorie_id->author_id != $current_user->ID) continue;
	if($current_user->ID == $selected_categorie_id->author_id){$author_id = 'dir';}else{$author_id = get_user_nicename($selected_categorie_id->author_id);}
		echo "<tr>";
		echo "<td>";
		echo $selected_categorie_id->id;
		echo "</td>";
		echo "<td>" . $selected_categorie_id->name;
		if(!empty($selected_categorie_id->delegated_user)) {echo"<br>(erstellt von: ".$author_id.")</td>";}else{echo "</td>";}
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->remind_date) . "</td>";
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->dead_line) . "</td>";
		echo '<td style="text-align:center;">' . return_status_named($selected_categorie_id->status) . "</td>";
		if(current_user_not_banned($selected_categorie_id->id) == true){
		?>
		<td>
		<form name="edit_categorie" method="post">
		<input name="task_ba_task_id" type="hidden" value="<?php echo $selected_categorie_id->id; ?>" size="10">
		<input name="task_ba_task_name" type="hidden" value="<?php echo $selected_categorie_id->name; ?>" size="255">
		<input name="task_ba_task_description" type="hidden" value="<?php echo $selected_categorie_id->description; ?>" size="1000">
		<input name="task_ba_task_remind_date" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->remind_date); ?>" size="10">
		<input name="task_ba_task_dead_line" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->dead_line); ?>" size="10">
		<input name="task_ba_task_status" type="hidden" value="<?php echo $selected_categorie_id->status; ?>" size="2">
		<input name="task_ba_task_author" type="hidden" value="<?php echo $selected_categorie_id->author_id; ?>" size="2">
		<input name="task_ba_task_delegated_user" type="hidden" value="<?php echo $selected_categorie_id->delegated_user; ?>" size="2">
		<input type="submit" name="taks_ba_edit_task" value=" Bearbeiten  ">
		<?php if($current_user->ID == $selected_categorie_id->author_id) { ?><input type="submit" name="taks_ba_delete_task" value=" Löschen  "><?php }?>
		</form>
		</td>
		<?php
		}else {
		echo "<td>Gesperrt</td>";
		
		}
		}
		
	echo "</table>";
}


function completed_tasks_available(){
	
	global $wpdb;
	$current_user = wp_get_current_user();
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE (delegated_user = '".$current_user->ID."' OR author_id = '".$current_user->ID."' OR delegated_user = 'all') AND status = '9' ORDER BY dead_line DESC"));
	
	if($tasks_counted > 0) {return true;}
	else {return false;}
	

}


function show_completed_tasks(){

	/* Global */
	global $wpdb;

	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";

	/* Wir erstellen hier den Table-Code für die Tabelle*/
	?>
	<h3>Beendete Aufgaben</h3>
	<table>
	<tr>
	<th style="width:50px; text-align:left;">ID</th>
	<th style="width:200px">Titel</th>
	<th style="width:100px">Erinnerung am</th>
	<th style="width:100px">Erledigen bis</th>
	<th style="width:150px">Status</th>
	</tr>
	<?php
	$current_user = wp_get_current_user();
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_tasks." WHERE (delegated_user = '".$current_user->ID."' OR author_id = '".$current_user->ID."' OR delegated_user = 'all') AND status = '9' ORDER BY dead_line DESC");
	foreach ( $selected_categorie as $selected_categorie_id ) {
	if($current_user->wp_user_level < get_option('task_ba_grouptasks')) continue;
	if($current_user->ID == $selected_categorie_id->author_id){$author_id = 'dir';}else{$author_id = get_user_nicename($selected_categorie_id->author_id);}
		echo "<tr>";
		echo "<td>";
		echo $selected_categorie_id->id;
		echo "</td>";
		echo "<td>" . $selected_categorie_id->name;
		if(!empty($selected_categorie_id->delegated_user)) {echo"<br>(erstellt von: ".$author_id.")</td>";}else{echo "</td>";}
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->remind_date) . "</td>";
		echo '<td style="text-align:center;">' . format_unix_to_date($selected_categorie_id->dead_line) . "</td>";
		echo '<td style="text-align:center;">' . return_status_named($selected_categorie_id->status) . "</td>";
		if(current_user_not_banned($selected_categorie_id->id) == true){
		?>
		<td>
		<form name="edit_categorie" method="post">
		<input name="task_ba_task_id" type="hidden" value="<?php echo $selected_categorie_id->id; ?>" size="10">
		<input name="task_ba_task_name" type="hidden" value="<?php echo $selected_categorie_id->name; ?>" size="255">
		<input name="task_ba_task_description" type="hidden" value="<?php echo $selected_categorie_id->description; ?>" size="1000">
		<input name="task_ba_task_remind_date" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->remind_date); ?>" size="10">
		<input name="task_ba_task_dead_line" type="hidden" value="<?php echo format_unix_to_date($selected_categorie_id->dead_line); ?>" size="10">
		<input name="task_ba_task_status" type="hidden" value="<?php echo $selected_categorie_id->status; ?>" size="2">
		<input name="task_ba_task_author" type="hidden" value="<?php echo $selected_categorie_id->author_id; ?>" size="2">
		<input name="task_ba_task_delegated_user" type="hidden" value="<?php echo $selected_categorie_id->delegated_user; ?>" size="2">
		<input type="submit" name="taks_ba_edit_task" value=" Bearbeiten  ">
		<?php if($current_user->ID == $selected_categorie_id->author_id) { ?><input type="submit" name="taks_ba_delete_task" value=" Löschen  "><?php }?>
		</form>
		</td>
		<?php
		}else {
		echo "<td>Gesperrt</td>";
		
		}
		}
		
	echo "</table>";
}


if(isset($_POST["task_ba_create_task"])){

if(check_form_datas() === true AND check_doubled_datas() === true) { 
	task_ba_create_task(); 
	writelogdata($logdata, 'Task '.$_POST["task_ba_task_name"].' is created by User '.$current_user->ID.'');
	
	$_POST["task_ba_task_id"] = '';
	$_POST["task_ba_task_name"] = '';
	$_POST["task_ba_task_description"] = '';
	$_POST["task_ba_task_remind_date"] = '';
	$_POST["task_ba_task_dead_line"] = '';
	$_POST["task_ba_task_status"] = '';
	$_POST["task_ba_task_author"] = '';
} else if (check_doubled_datas() === false) {
	$form_notice = 'Daten sind schon vorhanden';
} else { 
	$form_notice = check_form_datas(); 
}


}


if (isset($_POST["task_ba_update_task"]) && check_form_datas() === true){  //Wir UPDATEN mit folgendem Code den Eintrag
		/* Global */
	
	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	
	$_POST["task_ba_task_description"] = htmlentities($_POST["task_ba_task_description"]);
	
	$wpdb->update( $table_name_tasks, array( 'name' => $_POST["task_ba_task_name"],
											 'description' => $_POST["task_ba_task_description"],
											 'remind_date' => format_date_to_unix($_POST["task_ba_task_remind_date"]),
											 'dead_line' => format_date_to_unix($_POST["task_ba_task_dead_line"]),
											 'delegated_user' => $_POST["task_ba_task_delegated_user"],
											 'status' => $_POST["task_ba_task_status"],
											 'last_activity' => time() 
											  ),
											array( 'id' => $_POST["task_ba_task_id"] ) );
											
	writelogdata($logdata, 'Task '.$_POST["task_ba_task_id"].' has been updated by User '.$current_user->ID.' status:'.$_POST["task_ba_task_status"]);
	
	delete_ban_for_users($_POST["task_ba_task_id"]);
	
	
	if(!empty($_POST["task_ba_task_delegated_user"])) $wpdb->update( $table_name_tasks, array( 'delegated_user' => $_POST["task_ba_task_delegated_user"]), array( 'id' => $_POST["task_ba_task_id"] ) );
	
	
	
	
	
	$form_notice .=  '<font color="green"> Die Aufgabe "'.$_POST["task_ba_task_name"].'" wurden geupdatet!</font>';
	
	
	$_POST["task_ba_task_id"] = '';
	$_POST["task_ba_task_name"] = '';
	$_POST["task_ba_task_description"] = '';
	$_POST["task_ba_task_remind_date"] = '';
	$_POST["task_ba_task_dead_line"] = '';
	$_POST["task_ba_task_status"] = '';
	$_POST["task_ba_task_author"] = '';

	} else if(isset($_POST["task_ba_update_task"])){	$form_notice .=  '<font color="red"> Die Aufgabe "'.$_POST["task_ba_task_name"].'" konnte nicht geupdatet werden!</font><br>'; $form_notice .= check_form_datas(); 	}

$current_user = wp_get_current_user();

if ((isset($_POST["taks_ba_delete_task"]) AND $_POST["task_ba_task_author"] == $current_user->ID) OR (isset($_POST["taks_ba_delete_task"]) AND $current_user->wp_user_level == 10)){  //Wir LÖSCHEN mit folgendem Code den Eintrag
		/* Global */
	global $wpdb;
	$table_name_categorie = $wpdb->prefix . "task_ba_tasks";
	$wpdb->delete( $table_name_categorie, array( 'id' => $_POST["task_ba_task_id"] ) );
	
	writelogdata($logdata, 'Tasks '.$_POST["task_ba_task_id"].' deleted by user '.$current_user->ID.'');
	
	
	$form_notice =  '<p><font color="green"> Die Aufgabe "'.$_POST["task_ba_task_name"].'" wurden gelöscht!</font></p>';
	$_POST["task_ba_task_id"] = '';
	$_POST["task_ba_task_name"] = '';
	$_POST["task_ba_task_description"] = '';
	$_POST["task_ba_task_remind_date"] = '';
	$_POST["task_ba_task_dead_line"] = '';
	$_POST["task_ba_task_status"] = '';
	$_POST["task_ba_task_author"] = '';
	
	
	
	} else if (isset($_POST["taks_ba_delete_task"]) AND $_POST["task_ba_task_author"] != $current_user->ID) {
	$form_notice =  '<p><font color="red"> Die Aufgabe "'.$_POST["task_ba_task_name"].'" kann von dir nicht gelöscht werden!</font></p>';
	echo $_POST["task_ba_task_author"]. " and " .$current_user->ID;
	}
	
	
	
if (isset($_POST["task_ba_reset"])){ 

	$form_notice =  '<p><font color="grey"> Die Aufgabe "'.$_POST["task_ba_task_name"].'" wurden nicht bearbeitet!</font></p>';
	$_POST["task_ba_task_id"] = '';
	$_POST["task_ba_task_name"] = '';
	$_POST["task_ba_task_description"] = '';
	$_POST["task_ba_task_remind_date"] = '';
	$_POST["task_ba_task_dead_line"] = '';
	$_POST["task_ba_task_status"] = '';
	$_POST["task_ba_task_author"] = '';
}


function create_option_categorie_lists(){

	global $wpdb;

	$table_name_categorie = $wpdb->prefix . "task_ba_categorie";


	
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_categorie." ORDER BY name ASC");
	foreach ( $selected_categorie as $selected_categorie_id ) {
		echo '<option ';
		echo 'value="$selected_categorie_id->id"';
		if($_POST["task_ba_task_status"]==0)echo ' selected ';
		echo '>';
		echo $selected_categorie_id->name;
		echo "</option>";

	
		}



}




global $wpdb;

$table_name_tasks = $wpdb->prefix . "task_ba_tasks";

$important_tasks_counted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_tasks." WHERE author_id = %s AND remind_date < ".time()."", $current_user->ID));
	


if($_GET["task_ba_tab"] == 'important-tasks' AND empty($_POST["task_ba_task_name"]) OR $_GET["task_ba_tab"] == '' AND empty($_POST["task_ba_task_name"])){
$page_active = 'important-tasks'; 
} else if($_GET["task_ba_tab"] == 'all-tasks' AND empty($_POST["task_ba_task_name"])){ 
$page_active = 'all-tasks'; 
} else if($_GET["task_ba_tab"] == 'new-task' OR !empty($_POST["task_ba_task_name"]) ){ 
$page_active = 'new-task';
}



?>
<div>
	<ul class="task-ba-menu">

	<div></div>
	<li <?php if($page_active == 'important-tasks'){echo 'class="active"';} else {echo 'class="inactive"';} ?>>
		<a href="http://<?php echo  $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?page=task-ba-manage-tasks&task_ba_tab=important-tasks"; ?>">Fällige Aufgaben <?php echo "(".count_important_tasks_available().")" ?></a>
	</li>
	<li <?php if($page_active == 'all-tasks' ){echo 'class="active"';} else {echo  'class="inactive"';} ?>>
		<a href="http://<?php echo  $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?page=task-ba-manage-tasks&task_ba_tab=all-tasks"; ?>" >Alle Aufgaben <?php echo "(".count_all_tasks().")" ?></a>
	</li>
	<li <?php if($page_active == 'new-task'){echo 'class="active"';} else {echo  'class="inactive"';} ?>>
		<a href="http://<?php echo  $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?page=task-ba-manage-tasks&task_ba_tab=new-task"; ?>">Neue Aufgabe</a>
	</li>
	<div class="task-ba-menu-end"></div>
		<a class="task-ba-menu-end"></a>
	</ul>
</div>

<?php if($_GET["task_ba_tab"] == 'important-tasks' AND empty($_POST["task_ba_task_name"]) OR $_GET["task_ba_tab"] == '' AND empty($_POST["task_ba_task_name"])){ ?>
<div>
	<div id="task-ba-important-tasks" >
		<div>
			<h2>Fällige Aufgaben</h2>
			<?php if(important_tasks_available() === true){show_important_tasks(); ?>
			<?php }else{ echo 'Keine wichtigen Aufgaben zu bearbeiten';} ?>
		</div>
	</div>
</div>


<?php } else if($_GET["task_ba_tab"] == 'all-tasks' AND empty($_POST["task_ba_task_name"])){  ?>
<div id="task-ba-all-tasks" style="float:none;">
	<div>
		<h2>Alle deine Aufgaben</h2>
		<?php if(my_tasks_available() === true){ show_my_tasks();} ?>

	</div>
	<div>
		<?php if(delegated_tasks_available() === true) show_delegated_tasks(); ?>
	</div>

	<div>
		<?php if(group_tasks_available() === true) show_group_tasks(); ?>
	</div>
	<br>
	<?php if(completed_tasks_available() === true) { ?>
	<div>
		<a onclick="show_hide('show_completed_tasks')" style="cursor:pointer;">Beendete Aufgaben Zeigen</a>
	</div>
	<div id="show_completed_tasks" style="display:none;" >
		<?php if(completed_tasks_available() === true) show_completed_tasks(); ?>
	</div>
	<?php } ?>
</div>

<?php } else if($_GET["task_ba_tab"] == 'new-task' OR !empty($_POST["task_ba_task_name"]) ){  ?>

<div id="task-ba-new-task">
	<div>
		<h2><?php if(empty($_POST["task_ba_task_name"]) ){ echo "Neue Aufgabe"; }else{ echo "Aufgabe bearbeiten"; }?></h2>
		<?php echo $form_notice; ?>
	</div>
	<div>

	
		<form name="task_ba_create_task" id="task_ba_task_form" method="post">
		<?php if(!empty($_POST["task_ba_task_id"])){ set_ban_for_users($_POST["task_ba_task_id"]);} ?>
		<select name="task_ba_task_categorie" size="1" style="width:200px;">
     		<?php create_option_categorie_lists();?>
    		</select><br>
	
		<input name="task_ba_task_id" type="hidden" value="<?php echo $_POST["task_ba_task_id"]; ?>" size="10" required />
		<label>Titel</label><br>
		<input name="task_ba_task_name" type="text" size="30" maxlength="50" placeholder="Aufgabentitel" value="<?php echo $_POST["task_ba_task_name"]; ?>" required /><br>
		<label>Beschreibung</label><br>
		<textarea name="task_ba_task_description" type="text" cols="50" rows="10" maxlength="1000" placeholder="Aufgabe" ><?php echo $_POST["task_ba_task_description"]; ?></textarea><br>
		<label>Erinnerung am...</label><br>
		<input name="task_ba_task_remind_date" type="text" size="30" maxlength="10" placeholder="Erinnerung am <?php echo date("d.m.Y", time()+200000); ?>" value="<?php echo $_POST["task_ba_task_remind_date"]; ?>" required /> <br>
		<label>Erledigen am...</label><br>
		<input name="task_ba_task_dead_line" type="text" size="30" maxlength="25" placeholder="Zu erledigen bis <?php echo date("d.m.Y", time()+604800); ?>" value="<?php echo $_POST["task_ba_task_dead_line"]; ?>" required <?php if($current_user->ID != $_POST["task_ba_task_author"] AND $_POST["task_ba_task_author"] != '') echo 'readonly'; ?>/><br>
					
		<?php if(($current_user->ID == $_POST["task_ba_task_author"] OR $_POST["task_ba_task_author"] == '' ) AND get_user_role($current_user->ID) >= get_option('task_ba_delegate')){ ?><label>Delegieren an</label><br>
		<select name="task_ba_task_delegated_user" size="1" style="width:200px;">
     	<?php get_user_form_options();?>
    	</select><br>
    	<?php } else { ?>
    	<input name="task_ba_task_delegated_user" type="hidden" value="<?php echo $_POST["task_ba_task_delegated_user"] ?>" size="2">
    	<?php } ?>
		<label>Status</label><br>
		<select name="task_ba_task_status" size="1">
      		<option value="0" <?php if($_POST["task_ba_task_status"]==0)echo 'selected'; ?>>Noch nicht begonnen</option>
      		<option value="1" <?php if($_POST["task_ba_task_status"]==1)echo 'selected'; ?>>Begonnen</option>
      		<option value="2" <?php if($_POST["task_ba_task_status"]==2)echo 'selected'; ?>>In Bearbeitung</option>
      		<?php if($_POST["task_ba_task_status"]==5){ ?>
      		<option value="5" <?php if($_POST["task_ba_task_status"]==5)echo 'selected'; ?> disabled>Erinnert</option>
      		<?php  } ?>
      		<?php if($_POST["task_ba_task_status"]==7){ ?>
      		<option value="7" <?php if($_POST["task_ba_task_status"]==7)echo 'selected'; ?> disabled>Gemahnt</option>
      		<?php  } ?>
      		<option value="9" <?php if($_POST["task_ba_task_status"]==9)echo 'selected'; ?>>Beendet</option>
    	</select><br>
		<input name="task_ba_task_author" type="hidden" size="30" maxlength="25" value="<?php if($_POST["task_ba_task_author"] == ''){ echo $current_user->ID;} else {echo $_POST["task_ba_task_author"];} ?>"><br>
    	<br>
    	<?php if($_POST["task_ba_task_id"] == '') echo '<input type="submit" name="task_ba_create_task" value=" Neue Aufgabe erstellen  ">';?>
		<?php if($_POST["task_ba_task_id"] != '') echo '<input type="submit" name="task_ba_update_task" value=" Aufgabe updaten  ">';?>
		<input type="submit" name="task_ba_reset" value=" Abbrechen">
		</form>

	
	</div>
	
</div>

<?php } ?>

<?php javascript_codes(); ?>
<?php javascript_code_change_tasks_listing(); ?>
<?php







}


?>