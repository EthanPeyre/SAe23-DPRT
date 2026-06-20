#!/opt/lampp/bin/php
<?php
/*
 * Programme: mqtt_to_mysql.php
 * Description: Souscrit au bus MQTT (broker Mosquitto) via mosquitto_sub
 *              et insere dans la table mesure chaque metrique recue
 *              correspondant a un capteur existant en base.
 *
 * Format de topic : AM107/by-room/{roomName}/data
 * Exemple          : AM107/by-room/E101/data
 *
 * Format de payload (JSON, tableau de 2 objets) :
 * [
 *   { "temperature": 24.3, "humidity": 32, "co2": 431, ... },
 *   { "deviceName": "AM107-35", "room": "E105", "floor": 1, "Building": "E" }
 * ]
 *
 * Principe : pour chaque cle numerique du 1er objet (temperature, humidity, ...),
 * on cherche en base un capteur dont le nom_salle correspond a la piece et dont
 * le type_capt correspond a cette cle (insensible a la casse). Si trouve, on
 * insere la mesure. Les cles sans capteur correspondant (co2, tvoc, Latitude...)
 * sont simplement ignorees -> permet d'ajouter facilement d'autres capteurs plus tard.
 *
 * Ce script tourne en continu et doit etre lance en arriere-plan au demarrage
 * de la VM (voir crontab @reboot, fichier README ou TP).
 */

/* Parametres de connexion au broker MQTT du departement */
$host_mosquitto = "mqtt.iut-blagnac.fr";
$port_mosquitto = "8883";
$user_mosquitto = "student";
$pass_mosquitto = "student";

/* Acces a la base MySQL (chemin absolu car ce script est dans /opt/lampp/scripts/) */
include("/opt/lampp/htdocs/SAe23/mysql.php");

/*
 * Ouverture d'un pipe vers mosquitto_sub.
 * -F '%t|%p' : on demande un format topic puis payload, separes par '|',
 * ce qui evite l'ambiguite si le payload contenait des espaces.
 * On souscrit a tous les topics correspondant au pattern sensors/AM107/by-room/+/data
 */
$commande = "mosquitto_sub -h $host_mosquitto -p $port_mosquitto -u $user_mosquitto -P $pass_mosquitto -t 'sensors/AM107/by-room/+/data' -F '%t|%p'";
$pipe = popen($commande, "r");

if (!$pipe)
{
	die("Impossible de lancer mosquitto_sub. Verifiez l'adresse IP et que mosquitto-clients est installe.\n");
}

echo "En ecoute sur le bus MQTT (sensors/AM107/by-room/+/data)...\n";

while (!feof($pipe))
{
	$ligne = fgets($pipe);

	if ($ligne === false || trim($ligne) === "")
	{
		continue;
	}

	$ligne = trim($ligne);

	/* Separation topic / payload sur le premier '|' */
	$pos = strpos($ligne, "|");
	if ($pos === false)
	{
		echo "Ligne ignoree (format inattendu) : $ligne\n";
		continue;
	}

	$topic = substr($ligne, 0, $pos);
	$payload = substr($ligne, $pos + 1);

	/* Extraction du nom de la salle depuis le topic sensors/AM107/by-room/{room}/data */
	if (!preg_match('#sensors/AM107/by-room/([^/]+)/data#', $topic, $matches))
	{
		echo "Topic non reconnu, ignore : $topic\n";
		continue;
	}
	$nom_salle = $matches[1];

	/* Decodage du payload JSON */
	$donnees = json_decode($payload, true);

	if (!is_array($donnees) || !isset($donnees[0]) || !is_array($donnees[0]))
	{
		echo "Payload JSON invalide pour la salle $nom_salle : $payload\n";
		continue;
	}

	$mesures = $donnees[0]; // ex: temperature, humidity, co2, ...

	$date_mesure = date("Y-m-d");
	$horaire_mesure = date("H:i:s");

	/* Pour chaque metrique presente dans le payload */
	foreach ($mesures as $cle => $valeur)
	{
		/* On ignore les champs non numeriques (ex: futurs champs texte) */
		if (!is_numeric($valeur))
		{
			continue;
		}

		$nom_salle_safe = mysqli_real_escape_string($id_bd, $nom_salle);
		$cle_safe = mysqli_real_escape_string($id_bd, $cle);

		/*
		 * Recherche du capteur correspondant a cette salle et a ce type de mesure.
		 * type_capt doit donc contenir "temperature", "humidity", etc. (insensible
		 * a la casse) pour les capteurs que tu as crees dans ta table capteur.
		 */
		$requeteCapteur = "
			SELECT nom_capt
			FROM capteur
			WHERE nom_salle = '$nom_salle_safe'
			AND LOWER(type_capt) = LOWER('$cle_safe')
			LIMIT 1
		";

		$resultatCapteur = mysqli_query($id_bd, $requeteCapteur);

		if (!$resultatCapteur || mysqli_num_rows($resultatCapteur) == 0)
		{
			/* Pas de capteur correspondant en base pour cette metrique -> on ignore */
			continue;
		}

		$ligneCapteur = mysqli_fetch_assoc($resultatCapteur);
		$nom_capt = $ligneCapteur['nom_capt'];
		$nom_capt_safe = mysqli_real_escape_string($id_bd, $nom_capt);
		$valeur_safe = mysqli_real_escape_string($id_bd, $valeur);

		$requeteInsert = "
			INSERT INTO mesure (nom_capt, date, horaire, valeur)
			VALUES ('$nom_capt_safe', '$date_mesure', '$horaire_mesure', $valeur_safe)
		";

		if (mysqli_query($id_bd, $requeteInsert))
		{
			echo "Mesure inseree : salle=$nom_salle capteur=$nom_capt type=$cle valeur=$valeur\n";
		}
		else
		{
			echo "Erreur lors de l'insertion : " . mysqli_error($id_bd) . "\n";
		}
	}
}

pclose($pipe);
mysqli_close($id_bd);
?>
