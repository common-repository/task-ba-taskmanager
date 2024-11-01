<?php

/************************************List Page***********************************
*
*	Version 0.1
*	Zuletzt bearbeitet:
*	21.04.2014		
*
*
*/

// Die List-Page ist für das Anlegen von Listen-Kategorien zuständig


/*Schritt 2*/
add_action( 'admin_menu' , 'task_ba_categorie_page');

/*Schritt 1*/
function task_ba_categorie_page() {
add_submenu_page('task-ba-manage-tasks' , "Listenmanager (Aufgaben)" , "Listenmanager (Aufgaben)" , 'manage_categories' , 'task-ba-manage-lists' , 'task_ba_categorie');

}

/*Schritt 3*/
function task_ba_categorie() {
if ( !current_user_can( 'manage_categories' ))
{
wp_die( _( 'Du hast nicht die entsprechende Berechtigung'));
}


global $logdata;

/* Wir erstellen die Funktion zur Erstellung einer neuen Kategorie */
function task_ba_create_categorie() {

/* Global */
global $wpdb;
	

$table_name_categorie = $wpdb->prefix . "task_ba_categorie";


$wpdb->insert( $table_name_categorie, array( 'name' => $_POST["create_categorie"],
											 'description' => $_POST["create_categorie_description"]   ) );
	
writelogdata($logdata, 'List-Categorie '.$_POST["create_categorie"].' has been created!');

}

function check_form_datas_categorie() {

if(empty($_POST["create_categorie"])) $failure_remind_date .= 'Bitte benenne die Kategorie<br>';

if(empty($_POST["create_categorie_description"])) $failure_remind_date .= 'Bitte gebe eine Beschreibung ein<br>';

$notice_failure .= $failure_remind_date;
$notice_failure .= $failure_dead_line;



if(empty($notice_failure)) { 
	return true; 
}else{
	return false;
}

}


function check_category_double(){
	/* Global */
	global $wpdb;
	global $logdata;
	
	
	$table_name_categorie = $wpdb->prefix . "task_ba_categorie";
	$create_categorie = $_POST['create_categorie'];
	$categorie_available = $wpdb->get_var($wpdb->prepare("SELECT COUNT(name) FROM ".$table_name_categorie." WHERE name = %s", $create_categorie));

	
	if($categorie_available == 0 && check_form_datas_categorie() === true){
	task_ba_create_categorie();
	return true;
	} else {
	writelogdata($logdata, 'List-Categorie '.$_POST["create_categorie"].' could not create!');
	$form_notice .= "<font color=\"red\"><b>Listenkategorie kann nicht aufgenommen erstellt werden</b></font><br>";
	if($categorie_available > 0){
	$form_notice .= "<font color=\"red\">Kategorie ist schon vorhanden!</font>";
	}else if(!check_form_datas_categorie()){
	$form_notice .= "<font color=\"red\">Daten fehlen!</font>";
	}
	return $form_notice;
	}
	
	
}








function show_categories(){

	/* Global */
	global $wpdb;
	global $logdata;

	$table_name_categorie = $wpdb->prefix . "task_ba_categorie";

	/* Wir erstellen hier den Table-Code für die Tabelle*/
	?>
	<table>
	<tr>
	<th style="width:50px; text-align:left;">ID</th>
	<th style="width:200px">Kategorie</th>
	<th style="width:200px">Beschreibung</th>
	</tr>
	<?php
	
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_categorie." ORDER BY name ASC");
	foreach ( $selected_categorie as $selected_categorie_id ) {
		echo "<tr>";
		echo "<td>";
		echo $selected_categorie_id->id;
		echo "</td>";
		echo "<td>";
		echo $selected_categorie_id->name;
		echo "</td>";
		echo "<td>";
		if(strlen($selected_categorie_id->description)>50) echo substr($selected_categorie_id->description,0,50)."..."; 
		else echo $selected_categorie_id->description;  
		//echo $selected_categorie_id->description;
		echo "</td>";
		?>
		<td>
		<form name="edit_categorie" method="post">
		<input name="task_ba_categorie_id" type="hidden" value="<?php echo $selected_categorie_id->id; ?>" size="10">
		<input name="task_ba_categorie_name" type="hidden" value="<?php echo $selected_categorie_id->name; ?>" size="255">
		<input name="task_ba_categorie_description" type="hidden" value="<?php echo $selected_categorie_id->description; ?>" size="1000">
		<input type="submit" name="taks_ba_edit_categorie" value=" Bearbeiten  ">
		<input type="submit" name="taks_ba_delete_categorie" value=" Löschen  ">
		</form>
		</td>
		<?php
		}
		
	echo "</table>";
}

if (isset($_POST["taks_ba_delete_categorie"])){  //Wir LÖSCHEN mit folgendem Code den Eintrag
		/* Global */
	global $wpdb;
	$table_name_categorie = $wpdb->prefix . "task_ba_categorie";
	$wpdb->delete( $table_name_categorie, array( 'id' => $_POST["task_ba_categorie_id"] ) );
	
	$form_notice .= "<p><font color=\"green\"> Die Kategorie wurden gelöscht!</font></p>";
	}
	
	
	
	
	
if (isset($_POST["task_ba_update_categorie"])){  //Wir UPDATEN mit folgendem Code den Eintrag
		/* Global */
	global $wpdb;
	global $logdata;
	$table_name_categorie = $wpdb->prefix . "task_ba_categorie";
	
	if(check_form_datas_categorie()){ 
		$update_cmd = $wpdb->update( $table_name_categorie, array( 'name' => $_POST["create_categorie"],
											'description' => $_POST["create_categorie_description"]
											),
											array( 'id' => $_POST["task_ba_categorie_id"] ) );
		
		
		$current_user = wp_get_current_user();
		 writelogdata($logdata, 'List-Categorie '.$_POST["task_ba_categorie_id"].' ( '.$_POST["create_categorie"].' ) has been updated by user'.$current_user->ID.' !');
	
		$form_notice .= "<p><font color=\"green\"> Die Kategorie wurden geupdatet!</font></p>";
		$_POST["task_ba_categorie_id"] = '';
		$_POST["create_categorie"] = '';
		$_POST["create_categorie_description"] = '';
		
		
	} else {
		$form_notice .= '<p><font color="red"> Wichtige Daten fehlen!</font></p>';
	
	}

	}
	
if (isset($_POST["task_ba_reset"])){ 
	$_POST["task_ba_categorie_id"] = '';
	$_POST["create_categorie"] = '';
	$_POST["create_categorie_description"] = '';
}
	
	
/*Hier wird die Kategorie create_category_runme zur Eintragung der Kategorie eingefügt */
if (isset($_POST["task_ba_create_categorie"]) & $_POST["task_ba_create_categorie"] != ''){


$form_notice = check_category_double();

}


?>
<div>
<h3>Deine Listenkategorien</h3>
<?php show_categories(); ?>
</div>
<div>
<h3>Erstelle eine neue Listenkategorie (<a name="create_task" onclick="show_hide('task_ba_create_categorie')" style="cursor:pointer;" unselectable = "on"> Öffnen </a>)</h3>
<div id="notice-categorie-ba">
<?php echo $form_notice; ?>
</div>
<p>
<form name="task_ba_create_categorie" id="task_ba_create_categorie" method="post" style="display:<?php if(!empty($_POST["task_ba_categorie_id"])){ echo 'inline-block';}else{echo 'none';} ?>;">
<input name="task_ba_categorie_id" type="hidden" value="<?php echo $_POST["task_ba_categorie_id"]; ?>" size="10">
<input name="create_categorie" type="text" size="30" maxlength="25" placeholder="Kategorie Name" value="<?php echo $_POST["task_ba_categorie_name"]; ?>"><br>
<textarea name="create_categorie_description" type="text" cols="50" rows="10" maxlength="1000" placeholder="Beschreibung der Kategorie" ><?php echo $_POST["task_ba_categorie_description"]; ?></textarea>
    <br>
    <?php if($_POST["task_ba_categorie_id"] == '') echo '<input type="submit" name="task_ba_create_categorie" value=" Neue Kategorie erstellen  ">';?>
	<?php if($_POST["task_ba_categorie_id"] != '') echo '<input type="submit" name="task_ba_update_categorie" value=" Kategorie updaten  ">';?>
	<input type="submit" name="task_ba_reset" value=" Abbrechen">
</form>
</p>

</div>

<?php javascript_codes(); ?>

<?php




}




?>