<?php
class DAO {
    private $host="localhost";
    private $db="pays";
    private $user="user_pays";
    private $password="Pays_2020";
    private $bdd;
    private $error="";

    public function __construct() {
        try
        {
            // On se connecte à MySQL
            $this->bdd = new PDO('mysql:host='.$this->host.';dbname='.$this->db.';charset=utf8', $this->user, $this->password);
        }
        catch(Exception $e)
        {
            // En cas d'erreur, on affiche un message et on arrête tout
                die($this->error=$e->getMessage());
        }
    }
    public function getError() {
        return $this->error;
    }
    private function executeQuery($sql) {
        $reponse_pays=$this->bdd->query($sql);
        if (!$reponse_pays) {
            $this->error="Requete SQL invalide";
        }
        return $reponse_pays->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCountries($continent="",$region="") {
        $sql="SELECT `id_pays`, `libelle_pays`, `libelle_pays_en`, `population_pays`, `taux_natalite_pays`, `taux_mortalite_pays`, `esperance_vie_pays`, `taux_mortalite_infantile_pays`, `nombre_enfants_par_femme_pays`, `taux_croissance_pays`, `population_plus_65_pays`, t_pays.`continent_id`, t_pays.`region_id` FROM `t_pays`";
        if ($continent) {
            $sql.="LEFT JOIN t_continents ON (t_pays.continent_id=t_continents.id_continent)";
        } else {
            $sql.="LEFT JOIN t_continents ON (t_pays.continent_id=t_continents.id_continent) WHERE libelle_continent IN ('Afrique', 'Amérique Latine et Caraïbes', 'Amérique Septentrionale', 'Asie', 'Europe', 'Océanie') ORDER BY libelle_continent";
        }
        if ($region) {
            $sql.="LEFT JOIN t_regions ON (t_pays.region_id=t_regions.id_region)";
            if ($continent=""&&$region="") {
                $sql.="LEFT JOIN t_regions ON (t_pays.region_id=t_regions.id_region) WHERE libelle_region IN ('Afrique Centrale', 'Afrique Orientale', 'Afrique Septentrionale', 'Afrique australe', 'Afrique occidentale', 'Amérique centrale', 'Amérique du Sud','Asie de l\'Est', 'Asie de l\'Ouest', 'Asie du Centre et du Sud', 'Asie du Sud-Est', 'Australie Nouvelle Zélande', 'Caraïbes', 'Europe de l\'Est', 'Europe de l\'Ouest', 'Europe du Nord', 'Europe du Sud', 'Micronésie', 'Mélanésie', 'Polynésie') ORDER BY libelle_region";
            }
        }
        if ($region||$continent) {
            $sql.=" WHERE ";

            if ($continent&&!$region) {
                $sql.="id_continent IN (".$continent.")";
            }
            if ($region&&!$continent) {
                $sql.="id_region IN (".$region.")";
            }
            if ($continent&&$region) {
                $sql.="id_continent IN (".$continent.")";
                $sql.=" AND ";
                $sql.="id_region IN (".$region.")";
            }
        }
        if ($region=0) {
            $sql.="LEFT JOIN t_regions ON (t_pays.region_id=t_regions.id_region) WHERE libelle_region IN ('Afrique Centrale', 'Afrique Orientale', 'Afrique Septentrionale', 'Afrique australe', 'Afrique occidentale', 'Amérique centrale', 'Amérique du Sud','Asie de l\'Est', 'Asie de l\'Ouest', 'Asie du Centre et du Sud', 'Asie du Sud-Est', 'Australie Nouvelle Zélande', 'Caraïbes', 'Europe de l\'Est', 'Europe de l\'Ouest', 'Europe du Nord', 'Europe du Sud', 'Micronésie', 'Mélanésie', 'Polynésie') ORDER BY libelle_region";
        }
        return $this->executeQuery($sql);
    }
    
}
$dao=new DAO();
if ($dao->getError()) {
    print "Une erreur s'est produite";
}
print_r($dao->getCountries("",""));
?>