# Projet Symfony - Photo'Vergant
Damiens Florent - 2019

# Présentation du projet
Création d'un site pour partager mes photos prisent en Auvergne/Rhône-Alpes
Dans le but d'aller plus vite lors de la phase de développement j'ai décidé de me baser sur un template disponible sur le site: https://colorlib.com/ 
Le back-office à également été récupéré via un template, il s'agit de Adminity

## Durée du projet
Le projet a été étalé sur la fin de l'année 2019, entre Juillet et Décembre 2019.

| Type       |               Temps                |
|------------|:----------------------------------:|
| Back-End   | 6 mois (Juillet -> Décembre 2019)    |
| Mise en ligne   | Décembre 2019    |

## Les outils
De multiples outils ont été utilisés dans le but d'améliorer la qualité du code. On note la présence de:
* PHPStan: outils d'analyse statique du code
* Infection: mutation testing
* Behat: framework de test en langage naturel
* PHPunit: Pour les tests

## Documentation :
* Analyse de la base de données: MCD et MLD
* Maquette HTML/CSS 
* Mise en place de test unitaire, d'intégration et fonctionnel
		
## ToolKit
Pour lancer PHPStan:
`vendor/bin/phpstan analyse -l 4 src tests`

Pour lancer PHPUnit:
`bin/phpunit --group=XXX [--filter=functionName]`

Pour lancer Infection:
`vendor\bin\infection --threads=4 --coverage=var/coverage`