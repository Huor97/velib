# Affichage des vélibs disponible
Vous souhaitez afficher les stations de vélibs à proximité de l'Arc de Triomphe sur une borne tactile pour des clients (pas besoin de se soucier du responsif design). Pour cela, il faut aller chercher la donnée sur une API que fournit directement la ville de Paris en Open Data.
Exemple d'url de l'API: https://opendata.paris.fr/api/records/1.0/search/?dataset=velib-disponibilite-en-temps-reel&q=&facet=stationcode&refine.stationcode=16107
Une partie du code a déjà été réalisée, votre mission est de le terminer. Il faut maintenant relier les différentes fonctions entre elles pour pouvoir finaliser l'affichage.
### Le projet se compose en 2 parties:
- La sauvegarde de données en base de données.
- L'affichage de la donnée récupérée en base de données.
## Sauvegarde de données
Dans les faits, quand on récupère de la donnée en temps réel, la donnée va être récupérée sur un projet dédié qui va également sauvegarder la donnée sur une base de données. Ce projet sera lancé toute le cas présent, on fera la sauvegarde de données en bdd au chargement de la page.
### Ce qui est attendu:
- Récupérer la liste des stations sauvegardées en bdd et utiliser le résultat pour pouvoir récupérer la donnée sur l'API.
- Chaque station doit être présente dans la base, le cas échéant, effectuer une modification.
## Affichage de la donnée
Après que la donnée soit insérée, il faut la récupérer pour pouvoir l'aggicher sur l'écran.
### Ce qui est attendu:
- Pour chaque station, récupérer la donnée des diponiblités.
- Afficher la donnée dans la bulle sur la carte et dans l'encart assoncié sur le côté.
### Pour aller plus loin:(une fois que la version de base est finie)
- Utiliser une boucle pour générer le code "HTML" en récupérant le donnée pour toutes les stations avec une seule requête (attention à bien garder le nom des classes et des ids pour la compatibilité CSS)
