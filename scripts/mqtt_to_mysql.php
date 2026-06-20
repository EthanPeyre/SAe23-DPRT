#!/opt/lampp/bin/php
<?php
/*
 * Description: Subscription au broker Mosquitto via mosquitto_sub
 *              insert metric into the wished table
 *              according to an existing sensor.
 *
 * Topic : AM107/by-room/{roomName}/data
 *
 * Payload format (JSON) :
 * [
 *   { "temperature": 24.3, "humidity": 32, "co2": 431, ... },
 *   { "deviceName": "AM107-35", "room": "E105", "floor": 1, "Building": "E" }
 * ]
 *
 * Principe : for each digital key in the first subject (temperature, humidity, ...),
 * We are looking for a sensor whose room name corresponds to the part and whose sensor type corresponds to said key (break-insensitive).
 * If found we insert the measure. 
 * Keys without corresponding sensors (CO2, TVOC, Latitude, etc.) are simply ignored. 
 * This allows you to easily add other sensors later.
 *
 * This script runs continuously and must be launched in the background at startup of the VM (see crontab @reboot, README or TP file).
 */

/* Parameters for connecting to the MQTT broker from the department */
$host_mosquitto = "mqtt.iut-blagnac.fr";
$port_mosquitto = "8883";
$user_mosquitto = "student";
$pass_mosquitto = "student";

/*Give access to the MySQL data base (absolute path because this script is in /opt/lampp/scripts/) */
include("/opt/lampp/htdocs/SAe23/mysql.php");

/*
 * Opening a pipe to mosquitto_sub.
 * -F '%t|%p': we ask for a topic then payload format, separated by '|',
 * which avoids ambiguity if the payload contained spaces.
 * We subscribe to all the topics corresponding to the sensors/AM107/by-room/+/data pattern.
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

	/* Topic separation / payload on the first '|' */
	$pos = strpos($ligne, "|");
	if ($pos === false)
	{
		echo "Ligne ignoree (format inattendu) : $ligne\n";
		continue;
	}

	$topic = substr($ligne, 0, $pos);
	$payload = substr($ligne, $pos + 1);

	/* Extraction of the room name from the topic sensors/AM107/by-room/{room}/data */
	if (!preg_match('#sensors/AM107/by-room/([^/]+)/data#', $topic, $matches))
	{
		echo "Topic non reconnu, ignore : $topic\n";
		continue;
	}
	$nom_salle = $matches[1];

	/* Decoding the JSON payload */
	$donnees = json_decode($payload, true);

	if (!is_array($donnees) || !isset($donnees[0]) || !is_array($donnees[0]))
	{
		echo "Payload JSON invalide pour la salle $nom_salle : $payload\n";
		continue;
	}

	$mesures = $donnees[0]; // ex: temperature, humidity, co2, ...

	$date_mesure = date("Y-m-d");
	$horaire_mesure = date("H:i:s");

	/* For each metric present in the payload */
	foreach ($mesures as $cle => $valeur)
	{
		/* We ignore non-numeric fields (e.g., future text fields) */
		if (!is_numeric($valeur))
		{
			continue;
		}

		$nom_salle_safe = mysqli_real_escape_string($id_bd, $nom_salle);
		$cle_safe = mysqli_real_escape_string($id_bd, $cle);

		/*
		 * Search for the sensor corresponding to this room and this type of measurement.
		 * type_capt must therefore contain "temperature", "humidity", etc. (break-insensitive)
		 * for the sensors you have created in your sensor table.
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
			/* No corresponding sensor at the base for this metric -> we ignore */
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
