<?php

/************************************GENERAL FUNCTIONS***********************************
*
*	Version 1.0
*	Zuletzt bearbeitet:
*	05.05.2014		
*/


/*************************************
*
*	Hier erfolgt der Cronjobs mit täglicher Ausführung	
*	zur Erinnerung offenen Aufgaben
*
*/




/* USER-LEVEL mit Berechtigungen
 all = Display all user
 1 = subscriber
 2 = editor
 3 = author
 7 = publisher
10 = administrator
*/




function remember_open_tasks() {

	/*General-Functions*/
	global $logdata;
	
	/* Global */
	global $wpdb;
	
	/* Wir prüfen, ob die Notifications versendet werden sollen */
	$h = date('H'); 
	$m = date('i'); 
	$s = date('s');
	$current_time =  ($h*60*60) + ($m*60) + $s;  
	
	if(get_option('timezone_string') != '' ){
		date_default_timezone_set( get_option('timezone_string'));
	}
	
	
	
	if(get_option('task_ba_noti_time_sec') > $current_time ) return false;
	


	$table_name_tasks = $wpdb->prefix . "task_ba_tasks";
	$selected_categorie = $wpdb->get_results("SELECT * FROM ".$table_name_tasks." WHERE status NOT LIKE '9'");
	foreach ( $selected_categorie as $selected_categorie_id ) {
		if( $selected_categorie_id->remind_date <= time() AND $selected_categorie_id->dead_line > time() AND $selected_categorie_id->last_notice_mail <= time()){
			
			if( date("d.m.Y", $selected_categorie_id->last_notice_mail) == date("d.m.Y") ) continue;
				writelogdata($logdata, 'Task '.$selected_categorie_id->id.' is chosen for warning!');
			
				$empfaenger  =  get_user_email($selected_categorie_id->author_id);
				if($selected_categorie_id->delegated_user != '' AND $selected_categorie_id->delegated_user != 'all'){
					$empfaenger_cc .=  get_user_email($selected_categorie_id->delegated_user). ', ';
			
				} else if($selected_categorie_id->delegated_user == 'all'){
				
			
			
				}
			
		

		// Betreff
		$betreff = 'Erinnerung an offene Aufgabe';

		// Nachricht
		$nachricht = '
		<html>
		<head>
  			<title>Erinnerung an offener Aufgabe</title>
			</head>
			<body>
  				<p>Dies ist eine automatische Erinnerung an offene Aufgaben.</p>
  				<p>Für heute ist eine Erinnerung an die Aufgaben "'.$selected_categorie_id->name.'" eingetragen worden.</p>
  				Viele Grüße <br><br>
  				Dein Task BA
			</body>
		</html>
';		
		
		$nachricht = utf8_decode($nachricht);
		
		
			// für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
			$header  = 'MIME-Version: 1.0' . "\r\n";
			$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			//$header .= 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>' . "\r\n";
			$header .= 'Cc: '.$empfaenger_cc . "\r\n";
			$header .= 'X-Mailer: PHP/' . phpversion();

		// verschicke die E-Mail
		mail($empfaenger, $betreff, $nachricht, $header);
		writelogdata($logdata, 'User der Aufgabe '.$selected_categorie_id->id.' erinnert!');
		
		
		$wpdb->update( $table_name_tasks, array( 'status' =>  '5',
											 'last_notice_mail' => time(),
											  ),
											array( 'id' => $selected_categorie_id->id ) );
		
		}
	
	
	}
	
	
}

add_action('task_ba_hook_sent_mail', 'remember_open_tasks');
