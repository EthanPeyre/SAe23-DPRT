
# SAÉ23 – Mise en place d'une solution informatique pour l'entreprise

> Projet réalisé dans le cadre du BUT Réseaux et Télécommunications – IUT de Blagnac  
> Semestres 2 – 2025/2026

[![PHP](https://img.shields.io/badge/PHP-7.4+-8892BF.svg)](https://www.php.net/)  
[![Node-RED](https://img.shields.io/badge/Node--RED-v2.1.5-brightgreen.svg)](https://nodered.org/)  
[![Grafana](https://img.shields.io/badge/Grafana-v10.1-blue.svg)](https://grafana.com/)  
[![Docker](https://img.shields.io/badge/Docker-v24.0-blue.svg)](https://www.docker.com/)  
[![MySQL](https://img.shields.io/badge/MySQL-v8.0-orange.svg)](https://www.mysql.com/)

##  Description du projet

Ce projet a pour objectif de concevoir et déployer une solution informatique complète permettant la **visualisation de données issues de capteurs IoT** répartis dans les bâtiments de l'IUT.

La solution repose sur deux approches complémentaires :

###  Chaîne de traitement via Docker
Mise en place d'une stack de conteneurs pour collecter, stocker et visualiser les données en temps réel :
- **Mosquitto** – Broker MQTT pour la réception des données capteurs
- **Node-RED** – Programmation événementielle et routage des données
- **InfluxDB** – Base de données orientée séries temporelles
- **Grafana** – Visualisation et tableau de bord des métriques

###  Site web dynamique
Développement d'une interface web hébergée sur un serveur LAMPP :
- **Base de données MySQL** – Stockage structuré des bâtiments, salles, capteurs et mesures
- **Gestion des comptes** – Rôles Administrateur, Gestionnaire et Utilisateur
- **Automatisation** – Scripts PHP/Bash planifiés via crontab pour la collecte des données MQTT
- **Affichage des métriques** – Tableaux et graphiques (min, max, moyenne par capteur)

---

##  Contexte

Les données proviennent de capteurs déployés dans deux bâtiments de l'IUT. Chaque bâtiment dispose de deux capteurs et d'un gestionnaire dédié. L'objectif est d'offrir une interface conviviale permettant :
- la consultation des mesures en temps réel,
- l'affichage de statistiques (min, max, moyenne),
- une gestion sécurisée des accès par rôle.

---

##  Modèle de données

La base de données MySQL respecte les contraintes suivantes :

| Entité | Attributs principaux |
|---|---|
| **Bâtiment** | identifiant, nom, gestionnaire (login/mdp) |
| **Salle** | nom unique, type, capacité, bâtiment |
| **Capteur** | nom unique, type, unité, salle |
| **Mesure** | identifiant, date, horaire, valeur, capteur |

---

##  Pages du site web

| Page | Accès | Description |
|---|---|---|
| **Accueil** | Tous | Présentation du site, bâtiments, salles, mentions légales |
| **Administration** | Administrateur | Ajout/suppression de bâtiments, salles et capteurs |
| **Gestion** | Gestionnaire | Mesures et statistiques de son bâtiment |
| **Consultation** | Tous | Dernière mesure de toutes les salles |
| **Gestion de projet** | Tous | GANTT, outils collaboratifs, bilans individuels |

---

##  Technologies utilisées

- **Système** : GNU/Linux (Lubuntu) sur machine virtuelle
- **Langages** : HTML5, CSS3, PHP, JavaScript, Bash
- **Serveur web** : XAMPP (Apache + MySQL + PHP)
- **Conteneurs** : Docker (Mosquitto, Node-RED, InfluxDB, Grafana)
- **Protocole IoT** : MQTT
- **Versionning** : Git & GitHub

---

##  Livrables

| Livrable | Contenu | Date limite |
|---|---|---|
| **Séances** | Compte rendu des manioulations individuelles | Continu |
| **L1** | GANTT + schéma conception BD | 07/06/2026 à 18h |
| **L2 & L3** | Flow Node-RED + Dashboard Grafana | 14/06/2026 à 18h |
| **L4** | Version finale du projet + URL GitHub | 21/06/2026 à 18h |

---

##  Équipe

| Prénom Nom | Rôles |
|---|---|
| Lisa-Marie RAKOTOSON | Gestion de projet, Base de Données, MySQL |
| Julien DEWATINE | Conteneur Node-RED, InfluxDB, Grafana et Données MQTT |
| Mathéo TASSIN | Conteneur Node-RED, InfluxDB, Grafana et Données MQTT |
| Ethan PEYRE | Serveur web XAMPP et PHPMyAdmin |

---

## Contraintes

- **Environnement** : machine virtuelle
- **Système d’exploitation** : GNU/Linux
- **Langages autorisés** : HTML5, CSS3, PHP, Javascript, Bash, C, Python.
- Codes documentés (commentaires pertinents dans le code) **en anglais**
- Publication sur un **serveur web** dédié (xampp)
- Gestion de version via **Git et Github**

---
## Où installer les fichiers

- **scripts** : /opt/lampp/
- **SAe23** : /opt/lampp/htdocs
---

## 📄 Licence

Situation d'apprentissage et d'évaluation 23 – IUT de Blagnac, département RT – 2025/2026
