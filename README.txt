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

1 	Lancez le Register * en allant à l'adresse :  

		http://www.votredomaine.com/votredossier/admin/register.php

2 	Dans un autre onglet (ou une autre fenêtre) lancez l'Admin ** du wall en vous rendant à cette adresse :

		http://www.votredomaine.com/votredossier/admin

3 	Vous pouvez enfin lancer le mur public dans une fenêtre, un autre onglet ou même un autre ordinateur en vous rendant à la racine de votre mur :

		http://www.votredomaine.com/votredossier
	
* Le Register est en charge de la création de la base de données et de la captation des messages :
Vous devez impérativement laisser le Register ouvert durant toute la durée de la session. 
Lancez le dans un onglet de votre navigateur que vous garderez donc ouvert. 

** L'Admin vous permet de gérer votre wall :
-	Modération : A posteriori (par défaut) / A priori
-	Hashtag / RSS : Paramètres de recherche. Vous pouvez y saisir plusieurs hashtag Twitter ainsi qu'un flux RSS en les séparant par des virgules
-	Thèmes : voir rubrique associée
-	Avatars : Affichage ou non des avatars
-	Purge du mur : ATTENTION : Supprime tous les messages dans la base de données ! Vide integralement la base de données ! A utiliser en toute connaissance de cause ...

Thèmes :
--------

Vous pouvez créer vos propres thèmes pour l'affichage du wall public.
La gestion des thèmes est basique pour l'instant (CSS, pas de template dynamique).
Rendez-vous dans le dossier 'thèmes', copiez-collez et renommez le thème 'default', customisez CSS et images à votre convenance (les thèmes fournis le sont pour l'exemple)
Votre custom doit se trouver sur le serveur dans le dossier 'themes'.
Vous devez saisir son nom dans les options de l'admin puis relancer le wall public pour mettre à jour.

Infos techniques :
------------------

- PHP5 + SQLite
- SMS Enabler
- jQuery
- SimplePie


Author : @Hugobiwan & @dweez
Licence : Choix en cours
