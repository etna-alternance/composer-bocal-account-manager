# composer-bocal-account-manager
Wrapper PHP pour l'interaction avec l'API de console bocal

## Installation

`composer require etna/bocal-account-manager`

## Registering dans l'appli

Lors de la configuration de l'application silex il suffit de faire :

```
$app->register(new ETNA\Silex\Provider\BocalAccounts\BocalAccountsManagerProvider());
```

Le provider va se baser sur 3 variables d'environnement :

 - BOCAL_API_URL Qui est l'url de base de l'API bocal
 - BOCAL_API_KEY Le token bearer pour l'autorisation
 - BOCAL_READ_ONLY A mettre explicitement à false pour pouvoir persister des données, à ne mettre qu'en prod

## Utilisation dans l'appli

Le wrapper est désormais disponible dans `$app["bam"]` et expose les méthodes suivantes

 - listAccounts() Va chercher la liste complète des comptes
 - getAccount($account_id) Va chercher les informations concernant un compte donné
 - getAccountTelephone($account_id) Va chercher le téléphone lié au compte donné
 - createAccount($firstname, $lastname, $group, $email_etna) Crée un compte avec les informations données en paramètre
 - updateAccount($account_id, $account_data) Modifie les informations du compte donné
