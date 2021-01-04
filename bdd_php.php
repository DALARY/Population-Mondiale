<?php

try
{
	// On se connecte à MySQL
	$bdd = new PDO('mysql:host=localhost;dbname=pays;charset=utf8', 'user_pays', 'Pays_2020');
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
}

// Si tout va bien, on peut continuer

// On récupère tout le contenu de la table pays
$reponse = $bdd->query('SELECT id_continent,libelle_continent FROM t_continents ORDER BY libelle_continent');

// Si pas de continent on charge tous les pays (continent=MONDE) et on calcule les totaux pour tous les pays
if ((!isset($_GET["continent"])||!$_GET["continent"])) {
	$pays=$bdd->query('SELECT * FROM t_pays ORDER BY libelle_pays');
	
	$totaux=$bdd->query('SELECT \'Totaux\' AS libelle, 
	SUM(t_pays.population_pays) AS population,
    ROUND(AVG(t_pays.taux_natalite_pays), 1) AS natalite,
    ROUND(AVG(t_pays.taux_mortalite_pays), 1) AS mortalite,
    ROUND(AVG(t_pays.esperance_vie_pays), 1) AS vie,
    ROUND(AVG(t_pays.taux_mortalite_infantile_pays), 1) AS mort_inf,
    ROUND(AVG(t_pays.nombre_enfants_par_femme_pays), 1) AS nb_enfant,
    ROUND(AVG(t_pays.taux_croissance_pays), 1) AS croissance,
    SUM(t_pays.population_plus_65_pays) AS senior
	FROM t_pays');
}

// Si continent on charge tous les pays du continent et on calcule les totaux pour tous les pays
if (isset($_GET["continent"])&&$_GET["continent"]) {
	
	$regions = $bdd->query('SELECT id_region,libelle_region FROM t_regions WHERE continent_id='.$_GET["continent"].' ORDER BY libelle_region');
	
	$pays=$bdd->query('SELECT * FROM t_pays LEFT JOIN t_continents ON (t_pays.continent_id=t_continents.id_continent) WHERE id_continent='.$_GET["continent"].' ORDER BY libelle_pays');
	
	$totaux=$bdd->query('SELECT \'Totaux\' AS libelle, 
	SUM(t_pays.population_pays) AS population,
    ROUND(AVG(t_pays.taux_natalite_pays), 1) AS natalite,
    ROUND(AVG(t_pays.taux_mortalite_pays), 1) AS mortalite,
    ROUND(AVG(t_pays.esperance_vie_pays), 1) AS vie,
    ROUND(AVG(t_pays.taux_mortalite_infantile_pays), 1) AS mort_inf,
    ROUND(AVG(t_pays.nombre_enfants_par_femme_pays), 1) AS nb_enfant,
    ROUND(AVG(t_pays.taux_croissance_pays), 1) AS croissance,
    SUM(t_pays.population_plus_65_pays) AS senior
	FROM t_pays LEFT JOIN t_continents ON (t_pays.continent_id=t_continents.id_continent) WHERE id_continent='.$_GET["continent"]);
	
	
}

// Si région on charge tous les pays de la région et on calcule les totaux pour tous les pays
if (isset($_GET["region"])&&$_GET["region"]) {
	
	$pays=$bdd->query('SELECT * FROM t_pays LEFT JOIN t_regions ON (t_pays.region_id=t_regions.id_region) WHERE id_region='.$_GET["region"].' ORDER BY libelle_pays');
	
	$totaux=$bdd->query('SELECT \'Totaux\' AS libelle, 
	SUM(t_pays.population_pays) AS population,
    ROUND(AVG(t_pays.taux_natalite_pays), 1) AS natalite,
    ROUND(AVG(t_pays.taux_mortalite_pays), 1) AS mortalite,
    ROUND(AVG(t_pays.esperance_vie_pays), 1) AS vie,
    ROUND(AVG(t_pays.taux_mortalite_infantile_pays), 1) AS mort_inf,
    ROUND(AVG(t_pays.nombre_enfants_par_femme_pays), 1) AS nb_enfant,
    ROUND(AVG(t_pays.taux_croissance_pays), 1) AS croissance,
    SUM(t_pays.population_plus_65_pays) AS senior
	FROM t_pays LEFT JOIN t_regions ON (t_pays.region_id=t_regions.id_region) WHERE id_region='.$_GET["region"]);
} 





?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<form id="form_criteria">
	<select id="continent" name="continent" class="form-control" onchange="document.getElementById('region').value='';">
		<option value="">MONDE</option>
		<?php while ($donnees = $reponse->fetch(PDO::FETCH_ASSOC)) { ?>
			<option value="<?=$donnees["id_continent"]?>"<?php if (isset($_GET["continent"])&&$_GET["continent"]&&$_GET["continent"]==$donnees["id_continent"]) print " SELECTED";?>><?=$donnees["libelle_continent"];?></option>
		<?php } 
		$reponse->closeCursor(); // Termine le traitement de la requête
		?>
		
	</select><br>
	<select id="region" name="region"  class="form-control"><option value="">Sélectionner une région</option>
	<?php if (isset($_GET["continent"])&& $_GET["continent"]) { 
	while ($donnees = $regions->fetch(PDO::FETCH_ASSOC)) {
		print_r($donnees);
		?>
			<option value="<?=$donnees["id_region"]?>"<?php if (isset($_GET["region"])&&$_GET["region"]==$donnees["id_region"]) print " SELECTED";?>><?=$donnees["libelle_region"];?></option>
		<?php } 
		$regions->closeCursor(); // Termine le traitement de la requête
	} ?>
	</select><br>
	<button type="submit" class="btn btn-primary">Filtrer</button>
	</form><br>
	<table id="tableau" class="table table-striped" class="tableau">
		<thead>
		 <tr> 
			<th scope="col">PAYS</th>
			<th scope="col">POPULATION TOTALE (EN MILLIERS)</th>
			<th scope="col">TAUX DE NATALITE</th>
			<th scope="col">TAUX DE MORTALITE</th>
			<th scope="col">ESPERANCE DE VIE</th>
			<th scope="col">TAUX DE MORTALITE INFANTILE</th>
			<th scope="col">NOMBRE D'ENFANT(S) PAR FEMME</th>  
			<th scope="col">TAUX DE CROISSANCE</th>
			<th scope="col">POPULATION DE 65 ANS ET PLUS (EN MILLIERS)</th> 
		</tr>    
		</thead>
		<tbody>
			<?php 
			while ($donnees = $pays->fetch(PDO::FETCH_ASSOC))
			{?> 
				
				<tr scope="row">
				<th><?=$donnees["libelle_pays"]?></th>   
				<td><?=$donnees["population_pays"]?></td> 
				<td><?=$donnees["taux_natalite_pays"]?></td> 
				<td><?=$donnees["taux_mortalite_pays"]?></td> 
				<td><?=$donnees["esperance_vie_pays"]?></td> 
				<td><?=$donnees["taux_mortalite_infantile_pays"]?></td> 
				<td><?=$donnees["nombre_enfants_par_femme_pays"]?></td> 
				<td><?=$donnees["taux_croissance_pays"]?></td> 
				<td><?=$donnees["population_plus_65_pays"]?></td> 
				</tr>
				
				<?php } ?>
		</tbody> 
			<tfoot>
				<?php $donnees = $totaux->fetch(PDO::FETCH_ASSOC);?>
				<tr> 
					<th><?=$donnees["libelle"]?></th>   
					<td><?=$donnees["population"]?></td> 
					<td><?=$donnees["natalite"]?></td> 
					<td><?=$donnees["mortalite"]?></td> 
					<td><?=$donnees["vie"]?></td> 
					<td><?=$donnees["mort_inf"]?></td> 
					<td><?=$donnees["nb_enfant"]?></td> 
					<td><?=$donnees["croissance"]?></td> 
					<td><?=$donnees["senior"]?></td> 
				</tr>  
			</tfoot>
	</table>
</body>
</html>



