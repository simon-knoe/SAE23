<!-- final GANTT , collaborative tools screen shots,personnal synthesis of each member on his precise work, issues and proposed solutions, and a conclusion on satisfaction level of the technical specifications -->
<!DOCTYPE html>
<html>
    <head>
        <title>Gestion de projet SAE23</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/styles.css">
    </head>
    <header>
        <h1>Gestion de projet SAE23</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="consultation.php">Consultation des données</a></li>
                <li><a href="gestion.php">Gestion</a></li>
                <li><a href="administration.php">Administration</a></li>
                <li><a href="gestion-projet.php" class="active">Gestion de projet</a></li>
            </ul>
        </nav>
    </header>
    <body>
        <article>
            <h2>GANTT final</h2>
            <p>Le diagramme de Gantt final du projet SAE23 est présenté ci-dessous :</p>
            <img src="gantt_final.png" alt="Diagramme de Gantt final">
        </article>
        <article>
            <h2>Captures d’écran des outils collaboratifs utilisés</h2>
            <p>Pour notre projet, nous avons utilisé plusieurs outils collaboratifs comme Trello, un Drive google et un GitHub collaboratif</p>
            <img src="outils_collaboratifs.png" alt="Outils collaboratifs">
        </article>
        <article>
            <h2>Synthèse personnelle de chaque membre sur le travail précis réalisé</h2>
            <p>Chaque membre de l’équipe a rédigé une synthèse personnelle sur le travail qu’il a réalisé, les problèmes rencontrés et les solutions proposées. Voici un résumé des contributions de chaque membre :</p>
            <ul>
                <li>DEPOSIER Lilian : </li>
                <li>KNODLSEDER Simon : Dans la SAÉ 23, j'ai créé un site web avec des scripts PHP et des requêtes SQL pour gérer un réseau de capteurs, avec des fonctionnalités d'ajout, de suppression et un tableau de bord statistique (valeurs actuelles, minimum, maximum et moyenne). J'ai rencontré pas mal de difficultés avec la machine virtuelle lors des phases de test, notamment pour la lancer depuis mon PC donc j’ai du laisser le test a d’autres membres de mon équipe. </li>
                <li>VILAS-SAYSSAC Gabin : Mon travail s'est articulé autour de plusieurs axes. J'ai d'abord géré le développement web en créant l'architecture d'un site dynamique à l'aide de scripts Bash (header.sh, index.sh, footer.sh, web.sh) qui génèrent automatiquement la structure HTML pour afficher les données des capteurs (abandonné par la suite). Ensuite, j'ai assuré l'administration de la base de données MySQL en concevant et modifiant le schéma relationnel sur le serveur LAMPP, notamment en restructurant certaines tables via des requêtes SQL. En parallèle, j'ai pris en charge la gestion du versioning en initialisant le dépôt Git local et distant(changé par la suite), assurant ainsi la synchronisation entre mon environnement de développement sous Windows, la machine virtuelle Lubuntu et le dépôt GitHub partagé.</li>
            </ul>
        </article>
        <article>
            <h2>Conclusion : </h2>
            <p>En conclusion, ce projet de SAÉ 23 nous a permis d'atteindre un degré de satisfaction très élevé par rapport aux objectifs du cahier des charges initial. La double approche technique exigée a été réalisée avec succès. D'une part, la chaîne de traitement exploitant les conteneurs Docker est  opérationnelle, combinant Mosquitto pour le broker MQTT, Node-RED pour la programmation événementielle, InfluxDB et Grafana pour la visualisation des métriques. D'autre part, le site web dynamique, développé et publié sur le serveur LAMPP, répond parfaitement aux besoins de centralisation et de présentation des mesures. La base de données MySQL a été correctement structurée pour lier les entités de bâtiments, de salles, de capteurs et de mesures. De plus, l'accès aux pages d'administration et de gestion est proprement sécurisé grâce à l'utilisation de sessions adaptées aux différents rôles des utilisateurs. Malgré les défis techniques rencontrés lors de la configuration de l'environnement GNU/Linux sous Lubuntu et de la mise en place de la gestion de version collaborative via Git et GitHub, les solutions de dépannage mises en œuvre nous ont permis de stabiliser la version finale du livrable. Cette réalisation a été extrêmement formatrice, synthétisant l'ensemble de nos apprentissages en réseaux, en commandes systèmes et en développement web.</p>
        </article>
    </body>
</html>
