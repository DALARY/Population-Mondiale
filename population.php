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
$reponse_pays = $bdd->query('SELECT `libelle_pays`, `libelle_pays_en`, `population_pays`, `taux_natalite_pays`, `taux_mortalite_pays`, `esperance_vie_pays`, `taux_mortalite_infantile_pays`, `nombre_enfants_par_femme_pays`, `taux_croissance_pays`, `population_plus_65_pays` FROM `t_pays`');
$reponse_continent = $bdd->query('SELECT `id_continent`, `libelle_continent` FROM `t_continents` ORDER BY libelle_continent');

if (isset($_GET["continent"])&&$_GET["continent"]) {
$reponse_regions = $bdd->query('SELECT `libelle_region`, `id_region` FROM `t_regions` WHERE `continent_id`='.$_GET["continent"]);
}

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
	
	$reponse_regions = $bdd->query('SELECT id_region,libelle_region FROM t_regions WHERE continent_id='.$_GET["continent"].' ORDER BY libelle_region');
	
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/population.css">
    <script type="text/javascript" src="js/population.js"></script>
    <title>Population du  monde</title>
</head>
<body>
    <form id="form_criteria">
        <h1>Population du monde</h1>
        <div class="row menu" id="dropdown">
            <div class="form-group formulaire">
                <span>Par continent</span>
                <select class="form-control" id="continent" name="continent" onchange="changeContinent()">
                    <option value="">Monde</option>
                    <?php while ($donnees = $reponse_continent->fetch(PDO::FETCH_ASSOC))  { ?>
                    <option value="<?=$donnees['id_continent']?>"<?php if (isset($_GET["continent"])&&$_GET["continent"]==$donnees["id_continent"])print "SELECTED";?>><?=$donnees["libelle_continent"];?></option>
                    <?php } 
		            $reponse_continent->closeCursor(); // Termine le traitement de la requête
		            ?>
                </select>
            </div>
            <div class="form-group formulaire">
                <span>Pays par régions</span>
                <select class="form-control" id="region" name="region" onchange="changeRegion()">
                    <option value="">--</option>
                    <?php if (isset($_GET["continent"])&& $_GET["continent"]) {
                    while ($donnees = $reponse_regions->fetch(PDO::FETCH_ASSOC)) { 
                        print_r($donnees);?>
			        <option value="<?=$donnees["id_region"]?>"<?php if (isset($_GET["region"])&&$_GET["region"]==$donnees["id_region"]) print "SELECTED";?>><?=$donnees["libelle_region"];?></option>
		            <?php } 
		            $reponse_regions->closeCursor(); // Termine le traitement de la requête
		            }?>
                </select>
            </div>
        </div>
    </form>
    <main>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Pays</th>
                    <th scope="col">Population totale (en milliers)</th>
                    <th scope="col">Taux de natalité</th>
                    <th scope="col">Taux de mortalité</th>
                    <th scope="col">Espérence de vie</th>
                    <th scope="col">Taux de mortalité infantile</th>
                    <th scope="col">Nombre d'enfant(s) par femme</th>
                    <th scope="col">Taux de croissance</th>
                    <th scope="col">Population de 65 ans et plus (en milliers)</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($donnees = $pays->fetch(PDO::FETCH_ASSOC)) { ?>
            <?php print "<tr>"?>
            <?php print"<td>".$donnees["libelle_pays"]."</td>"?>
            <?php print"<td>".$donnees["population_pays"]."</td>"?>
            <?php print"<td>".$donnees["taux_natalite_pays"]."</td>"?>
            <?php print"<td>".$donnees["taux_mortalite_pays"]."</td>"?>
            <?php print"<td>".$donnees["esperance_vie_pays"]."</td>"?>
            <?php print"<td>".$donnees["taux_mortalite_infantile_pays"]."</td>"?>
            <?php print"<td>".$donnees["nombre_enfants_par_femme_pays"]."</td>"?>
            <?php print"<td>".$donnees["taux_croissance_pays"]."</td>"?>
            <?php print"<td>".$donnees["population_plus_65_pays"]."</td>"?>
            <?php print "</tr>"?>
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
    </main>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>