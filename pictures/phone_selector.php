<?php
// on se connecte à notre base de données
try {
    $bdd = new PDO('mysql:host=sql.free.fr;dbname=moryfodecisse', 'moryfodecisse', 'mory10');
}

catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$design = $_GET['design'];
$puissance = $_GET['puissance'];
$camera = $_GET['camera'];
$connectivite = $_GET['connectivite'];
$solidite = $_GET['solidite'];
$batterie = $_GET['batterie'];

$phone_data = $bdd->query('SELECT * FROM smartphonenotes');

$top3 = [0, 0, 0];
$selection = ["false", "false", "false"];

while ($donnees = $phone_data->fetch()) {
    $prix = [];
    $stockage = [];
    $phone_prix = $bdd->query('SELECT * FROM smartphonestocks WHERE idPhone='.$donnees["idPhone"]);
    $phone_specs_base = $bdd->query('SELECT * FROM smartphonespecs WHERE idPhone='.$donnees["idPhone"]);
    $phone_specs= $phone_specs_base->fetch();

    while ($donneesstocks = $phone_prix->fetch()) {
        array_push($prix, $donneesstocks['prix']);
        array_push($stockage, $donneesstocks['stockage']);
    }

    $score = 0;
    $nb_critere = 0;
    if ($design == 1) {
        $score += $donnees['design'];
        $nb_critere += 1;
    }
    if ($puissance == 1) {
        $score += $donnees['puissance'];
        $nb_critere += 1;
    }
    if ($camera == 1) {
        $score += $donnees['camera'];
        $nb_critere += 1;
    }
    if ($connectivite == 1) {
        $score += $donnees['reseau'];
        $nb_critere += 1;
    }
    if ($solidite == 1) {
        $score += $donnees['solidite'];
        $nb_critere += 1;
    }
    if ($batterie == 1) {
        $score += $donnees['batterie'];
        $nb_critere += 1;
    }

    $score = $score / $nb_critere;
    $data = '{
        "id": '. $donnees['idPhone'] .',
        "modele": "'. $donnees['phoneName'] .'",
        "design": '. $donnees['design'] .',
        "batterie": '. $donnees['batterie'] .',
        "camera": '. $donnees['camera'] .',
        "puissance": '. $donnees['puissance'] .',
        "reseau": '. $donnees['reseau'] .',
        "solidite": '. $donnees['solidite'] .',
        "prix": '. json_encode($prix) .',
        "stockage": '. json_encode($stockage) .',
        "taille": '. $phone_specs["taille"] .',
        "note": '. $donnees['ki'] .'
    }';

    if ($score > $top3[2]) {
        if ($score > $top3[1]) {
            if ($score > $top3[0]) {
                $top3[2] = $top3[1];
                $top3[1] = $top3[0];
                $top3[0] = $score;

                $selection[2] = $selection[1];
                $selection[1] = $selection[0];
                $selection[0] = $data;

            }
            else {
                $top3[2] = $top3[1];
                $top3[1] = $score;
                $selection[2] = $selection[1];
                $selection[1] = $data;
            }
        }
        else {
            $top3[2] = $score;
            $selection[2] = $data;
        }
    }

}

echo json_encode($selection);
?>
