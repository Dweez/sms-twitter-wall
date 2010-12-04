SMS-Twitter Wall Factory
================================

__ATTENTION__
	
	- Ce dépot est en cours de mise en place !
	- Les données ne sont pas protégées !  
	Prenez les mesures qui s'imposent pour bloquer l'accès au dossier d'installation
	
Plus d'infos :
--------------

Affichage temps réel de contributions envoyées par SMS et/ou Twitter sous forme d'un 'wall' personnalisable et modérable

	http://smswall.blogspot.com  
	http://www.sms-wall.org (A venir)

__SMS :__

-	Numéro d'envoi non surtaxé pour les participants
-	Materiel : Clé 3G + n'importe quelle carte SIM capable de recevoir des SMS
-	Utilisation de SMS Enabler pour la captation des SMS
-	...

__Twitter :__ 

-	Filtrage des twitts sur un ou plusieurs #hashtag
-	Fréquence de mise à jour paramétrable (@todo)
-	...

Modération des twitts et SMS a priori ou a posteriori.

Installation :
--------------

Posez les sources (le dossier /smswall) sur votre serveur. Vous pouvez le renommer comme bon vous semble. 
Assurez-vous que le safe_mode n'est pas activé sur votre serveur.

Lancez la création de la base de données en allant à l'adresse * :  

	http://www.votredomaine.com/votredossier/admin

Vous pouvez ensuite lancer le mur public dans une fenêtre en vous rendant à la racine de votre mur :

	http://www.votredomaine.com/votredossier
	
* Vous devez laisser l'administration ouverte durant toute la durée de la session. 
Lancez la dans un onglet de votre navigateur que vous garderez donc ouvert. 
Elle se charge de la captation des twits et de leur archivage. C'est ici aussi que vous pouvez configurer et modérer le mur.

Infos techniques :
------------------

- PHP5 + SQLite
- SMS Enabler
- jQuery
- SimplePie


Author : @Hugobiwan & @dweez
Licence : Choix en cours
