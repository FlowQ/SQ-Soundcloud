Projet Top Sounds - 18 Novembre 13

Basé sur le service Soundcloud et grâce à son API j'ai élaboré un système de recommandation de musique. Mon algorithme permet de déterminer quelles sont les chansons les plus à mêmes d'être aimées par un utilisateur en se basant sur les goûts des utilisateurs auxquels il est connecté.

Je développe mon projet en PHP, sans IDE particulière. 
Et mon service en ligne est mis à disposition sur un serveur cloud Amazon (AWS) http://ec2-54-229-202-97.eu-west-1.compute.amazonaws.com/SQ/Flow/ . 



- Pour tester le projet sur votre propre poste il vous faut créer une base de donnée MySQL nommée ‘soundcloud’ et dont les tables sont générées par le script ‘Scripts/soundcloud.sql’. De plus veillez à bien configurer le fichier de config, notamment le CALLBACK_URL.

De plus afin d’utiliser correctement le service, vous devez vous créer un compte Soundcloud, à partir du quel vous devez suivre au moins 20 autres utilisateurs ‘proches’, c’est à dire se suivant les uns les autres. Top Sounds nécessite l’indexation d’un nombre relativement important de sons afin de proposer des résultats intéressant, en dessous de 1000 titres synchronisés le système de recommandation n’est pas très efficace.

Une fois le compte Soundcloud créé, lancer index.php puis suivre le process d’identification. L’identification réalisée, vous accédez à la page ‘new.php’. Cliquer sur le lien ‘Cliquer ici pour lancer la recherche’, ce qui redirige vers vers le dashboard. En parallèle l’extraction des données est lancée. 

Le processus d’extraction est relativement long. En effet les requêtes faites à l’API Soundcloud sont longues, de l’ordre de 3 secondes pour une centaine de titres. La synchronisation de mon compte dure au total 6 minutes, pour 9 800 titres résultants. Afin de suivre l’évolution du process, vous pouvez consulter le fichier ‘log.txt’ qui logge les différentes étapes. Le processus est terminé lorsque la ligne ‘Durée totale calcul : 368.053787947’ est renseignée.

A partir de ce moment vous pouvez rafraichir la fenêtre et voir apparaitre la sélection. Le titre présentant le plus d’intérêt (classé numéro un) est affiché, il est possible de l’écouter en cliquant sur le bouton ‘Play’ orange. Le bouton ‘Coeur’ permet de marquer ce titre comme aimé, l’autre le supprime de Top Sounds. 
Les chiffres affichés sont extraits de Soundcloud et des statistiques propres au service.

- Le GitHub du projet est : https://github.com/FlowQ/SQ-Soundcloud


- Pour les statistiques, une tâche Cron tourne chaque nuit à 23h pour recueillir les données sur les utilisateurs du service.
Code de la tâche Cron :
0	23	*	*	*	*	php path/to/project/Scripts/stats.php