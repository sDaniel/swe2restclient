<?php 

	function convert_date_german($timestamp) {
		$timestamp;
		$date_array = explode("-", $timestamp);
		$year = $date_array[0];
		$month = $date_array[1];
		$day = $date_array[2];
		return $day.'/'.$month.'/'.$year;
	}

?>

<?php if(!isset($_POST['submit_profile_email'])) : ?>

	<form action="<?php print $FILE_URL; ?>?bereich=profilsuche" method="post">
	<label for="email">Geben Sie hier bitte die E-Mail-Adresse des zu suchenden Profiles an:</label>
	<input type="text" name="email" value="">
	<input type="submit" name="submit_profile_email" value="suchen">
	</form>
	
<?php else:
		$email = $_POST['email'];
		$uri = @file_get_contents('http://127.0.0.1:8080/webshopREST/profileManagement/profile/'.$email);
		
		// HTTP Status auslesen
		if(isset($http_response_header[0]))
		list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);
		
		// HTTP Status ueberpruefen
		if($status_code != 200) : ?>
		
		<div id="backlink"><a href="<?php print $FILE_URL; ?>?bereich=profilsuche">Anderes Profil suchen</a></div>
		<div class="error messages"><?php print 'Das Profil mit der E-Mail-Adresse "'.$email.'" existiert nicht.'; ?></div>
		
		<?php else:
		$simple_xml = (array) simplexml_load_string($uri);
	?>
		<div id="backlink"><a href="<?php print $FILE_URL; ?>?bereich=profilsuche">Anderes Profil suchen</a></div>
		<form action="<?php print $FILE_URL; ?>?bereich=profilsuche" method="post">
		<label for="companyName">Firmenname:</label>		<input type="text" name="companyName"  value="<?php print $simple_xml['companyName']; ?>"><br />
		<label for="firstName">Vorname:</label>				<input type="text" name="firstName" value="<?php print $simple_xml['firstName']; ?>"><br />
		<label for="lastName">Nachname:</label>				<input type="text" name="lastName" value="<?php print $simple_xml['lastName']; ?>"><br />
		<label for="telephone-number">Telefonnummer:</label><input type="text" name="telephone-number" value="<?php print $simple_xml['telephone-number']; ?>"><br />
		<input type="hidden" name="email" value="<?php print $_POST['email']; ?>">
		<input type="hidden" name="order-email" value="<?php print $_POST['email']; ?>">
		<input type="hidden" name="submit_profile_email" value="suchen">
		<input type="submit" name="submit_show_orders" value="Hole/Aktualisiere Bestellungen des Benutzers.">
		</form>
		
			<?php if(isset($_POST['submit_show_orders'])) :
				$order_email = $_POST['order-email'];
				$uri = @file_get_contents('http://127.0.0.1:8080/webshopREST/orderManagement/orders?email='.$order_email);
				
				// HTTP Status auslesen
				if(isset($http_response_header[0]))
				list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);
				
				// HTTP Status ueberpruefen
				if($status_code != 200) : ?>
				
				<div class="error messages"><?php print 'Zu diesem Benutzer existieren keine Bestellungen.'; ?></div>
				
				<?php else:		
				
				$simple_xml = (array) simplexml_load_string($uri);
				
				$bestellungen = $simple_xml['order'];
				
				$attributes = $bestellungen[0]->attributes();
				
				?>
				
				<div id="bestellungen">
        <h2>Bestellungen des Benutzers:</h2>
          <?php 
            foreach($bestellungen as $bestellung) :
            
              $attributes = $bestellung->attributes();
              $date = convert_date_german($bestellung->timestamp);
          ?>
						<div class="bestellung">
							<label for="bestellung-id">Bestellnummer:</label><div id="bestellung-id"><?php print $attributes['id']; ?></div>
							<label for="bestellung-datum">Bestelldatum:</label><div id="bestellung-datum"><?php print $date; ?></div>
						</div>
						
          <?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
	
<?php endif; ?>
