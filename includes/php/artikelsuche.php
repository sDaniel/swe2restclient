﻿<?php 
	if(!isset($_POST['submit_article_articleno'])) :
?>

	<form action="<?php print $FILE_URL; ?>?bereich=artikelsuche" method="post">
	<label for="articleno">Geben Sie hier bitte die zu suchende Artikelnummer ein:</label>
	<input type="text" name="articleno" value="">
	<input type="submit" name="submit_article_articleno" value="suchen">
	</form>
	
<?php else:
		$articleno = $_POST['articleno'];
		$uri = @file_get_contents('http://127.0.0.1:8080/webshopREST/articleManagement/article/'.$articleno);
		
		// HTTP Status auslesen
		if(isset($http_response_header[0]))
		list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);
		
		// HTTP Status ueberpruefen
		if($status_code != 200) : ?>
		
		<div id="backlink"><a href="<?php print $FILE_URL; ?>?bereich=artikelsuche">Anderen Artikel suchen</a></div>
		<div class="error messages"><?php print 'Der Artikel mit der Artikelnummer "'.$articleno.'" existiert nicht.'; ?></div>
		
		<?php else:
		
		
		$simple_xml = simplexml_load_string($uri);
		
		$price = $simple_xml->price;
		str_replace('.', ',', $price);
		$price = $price.'€';
		
		$attributes = $simple_xml->attributes();

	?>
	
	<div id="backlink"><a href="<?php print $FILE_URL; ?>?bereich=artikelsuche">Anderen Artikel suchen</a></div>
	<form action="<?php print $FILE_URL; ?>?bereich=artikelsuche" method="post">
	<label for="articleNo">Artikelnummer:</label>		<input type="text" name="articleNo"  value="<?php print $attributes->articleNo; ?>"><br />
	<label for="name">Artikelname:</label>				<input type="text" name="name" value="<?php print $simple_xml->name; ?>"><br />
	<label for="description">Beschreibung:</label>				<input type="text" name="description" value="<?php print $simple_xml->description; ?>"><br />
	<label for="stock">Auf Lager:</label><input type="text" name="stock" value="<?php print $simple_xml->stock; ?>"><br />
	<label for="price">Zum Preis:</label><input type="text" name="price" value="<?php print $price; ?>">
	</form>
	<?php endif; ?>
<?php endif; ?>