<?php



/************************************Options Page***********************************
*
*	Version 1.0
*	Zuletzt bearbeitet:
*	04.05.2014		
*
*
*/

// This Page is for managing the options of the Plugin


/*Schritt 2*/
add_action( 'admin_menu' , 'task_ba_options_page');

/*Schritt 1*/
function task_ba_options_page() {
add_options_page("Task BA Taskmanager" , "Task BA Taskmanager" , 'manage_categories' , 'task-ba-manage-options' , 'task_ba_options');

}

/*Schritt 3*/
function task_ba_options() {
if ( !current_user_can( 'manage_categories' ))
{
wp_die( _( 'Du hast nicht die entsprechende Berechtigung'));
}

/*General-Functions*/
	global $wpdb;
 	global $logdata;
	$version = '1.0';
	
	
	
	if(isset($_POST['task_ba_edit_options'])){
	update_option( 'task_ba_grouptasks', $_POST['task_ba_grouptasks'] );
	update_option( 'task_ba_delegate', $_POST['task_ba_delegate']);
	update_option( 'task_ba_noti_time_sec', $_POST['task_ba_noti_time_sec']);
	
	
	update_option( 'task_ba_email_adress', $_POST['task_ba_email_adress'] );
	update_option( 'task_ba_email_connection_type',  $_POST['task_ba_email_connection_type']);
	update_option( 'task_ba_email_username',  $_POST['task_ba_email_username'] );
	update_option( 'task_ba_email_password',  $_POST['task_ba_email_password'] );
	update_option( 'task_ba_email_port', $_POST['task_ba_email_port'] );
	update_option( 'task_ba_email_connection_security', $_POST['task_ba_email_connection_security'] );
	
	}

	
	/* USER-LEVEL mit Berechtigungen
 	all = Display all user
	1 = subscriber
 	2 = editor
 	3 = author
 	7 = publisher
	10 = administrator
	*/

	//get_option('task_ba_noti_time_sec')
	
	
if($version == '1.0'){
	echo "<h3>Einstellungen von BlogAthlet's Taskmanager</h3>";	
	echo '<form name="task_ba_mananage_options" id="task_ba_create_categorie" method="post">';	
	
	echo '<table id="table-task-ba-options">';
	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_grouptasks-label">Ab welchem Rang kann man Gruppenaufgaben erstellen?</label>';
		echo '</td>';
	
		echo '<td>';
		echo '<select name="task_ba_grouptasks" size="1" >';	
			echo '<option value="1" ';	
			if(get_option('task_ba_grouptasks') == 1) echo ' selected ';
			echo '>';	
			echo 'Abonnent';
			echo '</option>';
			
			echo '<option value="2" ';	
			if(get_option('task_ba_grouptasks') == 2) echo ' selected ';
			echo '>';	
			echo 'Mitarbeiter';
			echo '</option>';
			
			echo '<option value="3" ';	
			if(get_option('task_ba_grouptasks') == 3) echo ' selected ';
			echo '>';	
			echo 'Author';
			echo '</option>';
			
			echo '<option value="7" ';	
			if(get_option('task_ba_grouptasks') == 7) echo ' selected ';
			echo '>';	
			echo 'Redakteur';
			echo '</option>';
			
			echo '<option value="10" ';	
			if(get_option('task_ba_grouptasks') == 10) echo ' selected ';
			echo '>';	
			echo 'Administrator';
			echo '</option>';
		echo '</select>';
		echo '</td>';
	echo '</tr>';
	
	
	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_grouptasks-label">Ab welchem Rang kann man Aufgaben deligieren?</label>';
		echo '</td>';
	
		echo '<td>';
			echo '<select name="task_ba_delegate" size="1" >';	
				echo '<option value="1" ';	
				if(get_option('task_ba_delegate') == 1) echo ' selected ';
				echo '>';	
				echo 'Abonnent';
				echo '</option>';
			
				echo '<option value="2" ';	
				if(get_option('task_ba_delegate') == 2) echo ' selected ';
				echo '>';	
				echo 'Mitarbeiter';
				echo '</option>';
			
				echo '<option value="3" ';	
				if(get_option('task_ba_delegate') == 3) echo ' selected ';
				echo '>';	
				echo 'Author';
				echo '</option>';
			
				echo '<option value="7" ';	
				if(get_option('task_ba_delegate') == 7) echo ' selected ';
				echo '>';	
				echo 'Redakteur';
				echo '</option>';
			
				echo '<option value="10" ';	
				if(get_option('task_ba_delegate') == 10) echo ' selected ';
				echo '>';	
				echo 'Administrator';
				echo '</option>';
		echo '</select>';
		echo '</td>';
	echo '</tr>';
	
	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_noti_time_sec-label">Wann sollen Erinnerungen verschickt werden?</label>';
		echo '</td>';
	
		echo '<td>';
			echo '<select name="task_ba_noti_time_sec" size="1" >';	
				
				for($x = 0; $x <= 23; $x++){
				echo '<option value="'.($x*3600).'" ';	
				if(get_option('task_ba_noti_time_sec') == ($x*3600)) echo ' selected ';
				echo '>';	
				echo 'ca. '.$x.':00';
				echo '</option>';
				}
			
				
			
				
		echo '</select>';
		echo '</td>';
	echo '</tr>';
	
	}


if($version == '1.1'){	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_email_adress-label">Wie lautet der Host?</label>';
		echo '</td>';
		
		echo '<td>';
			echo '<input type="text" name="task_ba_email_adress" placeholder="email@mail.com" value="'.get_option('task_ba_email_adress').'"></input>';
		echo '</td>';
	echo '</tr>';
	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_email_connection_type-label">POP3 oder IMAP?</label>';
		echo '</td>';
		
		echo '<td>';
			echo '<select name="task_ba_email_connection_type" size="1" >';	
					echo '<option value="/pop3" ';	
					if(get_option('task_ba_email_connection_type') == '/pop3') echo ' selected ';
					echo '>';	
					echo 'POP3';
					echo '</option>';
					
					echo '<option value="/imap" ';	
					if(get_option('task_ba_email_connection_type') == '/imap') echo ' selected ';
					echo '>';	
					echo 'IMAP';
					echo '</option>';
					
			echo '</select>';
		echo '</td>';
	echo '</tr>';
	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_email_username-label">Welchen Benutzernamen benötigt der Zugang?</label>';
		echo '</td>';
		
		echo '<td>';
			echo '<input type="text" name="task_ba_email_username" placeholder="username" value="'.get_option('task_ba_email_username').'"></input>';
		echo '</td>';
	echo '</tr>';
	
	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_email_password-label">Passwort für den E-Mail-Zugriff?</label>';
		echo '</td>';
		
		echo '<td>';
			echo '<input type="password" name="task_ba_email_password" placeholder="Passwort" value="'.get_option('task_ba_email_password').'"></input>';
		echo '</td>';
	echo '</tr>';
	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_email_port-label">Welcher Port?</label>';
		echo '</td>';
		
		echo '<td>';
			echo '<input type="text" name="task_ba_email_port" placeholder="993" value="'.get_option('task_ba_email_port').'"></input>';
		echo '</td>';
	echo '</tr>';
	
	
	echo '<tr>';
		echo '<td>';
		echo '<label id="task_ba_email_connection_security-label">Welche Zugangseinstellung sind notwendig?</label>';
		echo '</td>';
		
		echo '<td>';
			echo '<select name="task_ba_email_connection_security" size="1" >';	
					
					echo '<option value="/tls/novalidate-cert" ';	
					if(get_option('task_ba_email_connection_security') == '/tls/novalidate-cert') echo ' selected ';
					echo '>';	
					echo 'TLS, ohne Zertifikats-Check';
					echo '</option>';
					
					echo '<option value="/ssl/novalidate-cert" ';	
					if(get_option('task_ba_email_connection_security') == '/ssl/novalidate-cert') echo ' selected ';
					echo '>';	
					echo 'SSL, ohne Zertifikats-Check';
					echo '</option>';
					
					echo '<option value="/notls" ';	
					if(get_option('task_ba_email_connection_security') == '/notls') echo ' selected ';
					echo '>';	
					echo 'keine Authentifizierung';
					echo '</option>';
					
					
					 
			echo '</select>';
		echo '</td>';
	echo '</tr>';
	
	echo '<tr>';
		echo '<td>';
			if(imap_open("{".get_option('task_ba_email_adress').":".get_option('task_ba_email_port').get_option('task_ba_email_connection_security')."}", get_option('task_ba_email_username'), get_option('task_ba_email_password'))) 
			{echo 'Verbindung fehlerfrei';
			}else{
			echo 'Verbindung fehlerhaft';
			}
		echo '</td>';
	echo '</tr>';
	}
	
	
	echo '</table>';
	
	echo '<input type="submit" name="task_ba_edit_options" value=" Einstellungen speichern  ">';	
	echo '<input type="submit" name="task_ba_reset" value=" Abbrechen">';	
	echo '</form>';	
	









}

?>