<?php
/* gestion_projet.php
 * Project management page – accessible to everyone.
 */

$page_title   = 'Gestion de projet – SAE23 IUT Blagnac';
$current_page = 'projet';

require_once 'header.php';
?>

<main>

    <section>
        <h2>Diagramme de GANTT</h2>
        <p>Planning prévisionnel et réel du projet SAE23.</p>
        <p>
            <a href="../L1_DEWATINE_PEYRE_RAKOTOSON_TASSIN.gan" target="_blank">
                <img class="aspects-techniques" src="../images/gantt.png" alt="Notre Diagramme de GANTT" />
            </a>
        </p>
    </section>

    <section>
        <h2>Outils collaboratifs utilisés</h2>
        <p>Captures d'écran et description des outils mis en œuvre tout au long du projet.</p>
        <table>
            <thead>
                <tr>
                    <th>Outil</th>
                    <th>Usage</th>
                    <th>Visuel</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Git / GitHub</td>
                    <td>Gestion des versions du code source, branches, pull requests, revues de code.</td>
                    <td>
                        <a href="https://github.com/EthanPeyre/SAe23-DPRT" target="_blank"> 
                            <img class="aspects-techniques" src="../images/github.png" alt="Notre Github" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>Tableau d'avancement</td>
                    <td>Mis a jour chaque séance d'autonomie, nos permet de reprendre ou l'on s'etait arreter. Remplacement du Trello</td>
                    <td>
                        <img class="aspects-techniques" src="../images/tableau_avancement.png" alt="Notre Tableau d'avancement" />
                    </td>
                </tr>
                <tr>
                    <td>PhpMyAdmin</td>
                    <td>Conception et vérification du schéma de base de données MySQL.</td>
                    <td>
                        <img class="aspects-techniques" src="../images/phpmyadmin.png" alt="Notre table SQL" />
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    <section>
        <h2>Synthèses individuelles des membres</h2>

        <article>
            <h3>Dewatine Julien</h3>
            <p><strong>Travaux réalisés :</strong> Dashboard Grafana, script crontab d'automatisation, mise en place de la chaîne Docker, configuration de Mosquitto et Node-RED, flow de souscription MQTT.</p>
            <p><strong>Problèmes rencontrés :</strong> Conflits de ports entre conteneurs Docker ; résolu en modifiant le mapping dans la commande <em>docker run</em>.</p>
            <p><strong>Solutions proposées :</strong> Utilisation de l'option <em>--restart=always</em> pour garantir le redémarrage automatique des conteneurs.</p>
        </article>

        <article>
            <h3>Tassin Mathéo</h3>
            <p><strong>Travaux réalisés :</strong> Dashboard Grafana, script crontab d'automatisation, mise en place de la chaîne Docker, configuration de Mosquitto et Node-RED, flow de souscription MQTT.</p>
            <p><strong>Problèmes rencontrés :</strong> Faute de frappe sur les capteurs, capteur ne prenait aucune données.</p>
            <p><strong>Solutions proposées :</strong> Changement de l'adresse IP de la source de données InfluxDB sur le Grafana et IP de la VM sur NodeRed.</p>
        </article>

        <article>
            <h3>Peyre Ethan</h3>
            <p><strong>Travaux réalisés :</strong> Développement des pages PHP du site web, gestion des sessions, page Administration et page Gestionrequêtes PHP pour l'insertion et la consultation des mesures.</p>
            <p><strong>Problèmes rencontrés :</strong> Securisation des accès et redirection après connexion selon le rôle.</p>
            <p><strong>Solutions proposées :</strong> Vérification systématique du rôle en session en début de chaque page protégée.</p>
        </article>

        <article>
            <h3>Rakotoson Lisa-Marie</h3>
            <p><strong>Travaux réalisés :</strong> rédaction de pages html et de la documentation, conception du schéma Entité-Association, création de la base MySQL.</p>
            <p><strong>Problèmes rencontrés :</strong> Synchronisation des timestamps entre InfluxDB et MySQL.</p>
            <p><strong>Solutions proposées :</strong> Normalisation du format de date <em>YYYY-MM-DD HH:MM:SS</em> dans le script de récupération.</p>
        </article>

    </section>

    <section>
        <h2>Conclusion – Degré de satisfaction du cahier des charges</h2>
        <p>
            L'ensemble des fonctionnalités obligatoires du cahier des charges a été réalisé :
            chaîne Docker opérationnelle, dashboard Grafana avec les 4 capteurs, site web dynamique
            hébergé sur lampp avec les pages Accueil, Consultation, Administration, Gestion et Gestion
            de projet, base de données MySQL conforme au modèle conceptuel, script de récupération MQTT
            et automatisation via crontab.
        </p>
        <p>
            Les fonctionnalités avancées (chiffrement MD5 des mots de passe) ont également été intégrées. 
            Le projet est versionné sur GitHub avec des commits réguliers, attestant d'un travail collaboratif continu.
        </p>
        <p>
            Le groupe estime avoir atteint un degré de satisfaction élevé vis-à-vis du cahier des charges.
        </p>
    </section>

</main>

<?php
require_once 'footer.html';
?>