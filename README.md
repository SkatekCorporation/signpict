![Google logo](https://www.skatek.net//img/logo-skatek-2.png "Skatek Corporation logo")
# Signateur d'images

## But
Aider les photographes à integrer rapidement leur logo dans les photos prises avec leurs appareils pour aller rapidement.

## Utilisation
Jusque là, l'Utilisation n'est que dans la ligne de commande.
```php index.php```.
Dans ce cas, le dossier source des photos (qui ne seront pas modifiées) est ```images/source/```. et le dossier de destination est ```images/destination```. Le logo se trouvera dans le dossier ```images/logo/```.
Le dossier temporaire est ```tmp```.

### Procedure d'Utilisation
Après avoir configurer votre ordinateur et installé php, voici quelques étapes à suivre.
#### First
Mettez les images que vous voulez copyrighter dans le dossier ```images/source/```. Et votre logo dans le dossier ```images/logo/```.
#### Second
Ensuite, ouvrez votre terminal ou console sur Windows. Deplacez-vous dans le dossier principal et taper la commande ```php index.php```
#### Third
Allez verifier vos images dans le dossier ```images/destination```

## Autres informations
Vous pouvez personaliser certaines choses telle que le dossier source ou le dossier de destination en ajoutant des drapeaux dans la ligne de commande.
```
php index.php -s dossier_source -d dossier_de_destination
```
Vous pouvez egalement nettoyer le dossier de destination avec ceci:
```
php index.php --clean destination [d, dest]
```

## Remerciements
* Skatek Corporation
* Souvenance Kavunga
* Contributeurs

## Remarques ou suggestions ?
Faites vos remarques et suggestions en toute tranquiliter pour nous aider à progresser. [Cliquer ici](https://skatek.net/contacts?subject=Remarques%2FCritiques%2FSignPicts&af=enabled#contact-form)