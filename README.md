# PHP Api

Cet exercice propose une implémentation orientée service d'une API permettant de trier un tableau de valeurs fourni en query parameter.

Trois versions de cette API ont été construites pour tenter de jauger les avantages et les inconvénients de chacune, trouvables sur la [page github](https://github.com/RathGate/corbel_b2_php_tp1) du projet.

La version `main` définitive, celle de ce rendu, cherche à implémenter les dernières pistes d'amélioration abordées en cours ainsi qu'une totale gestion des erreurs, un autoloader manuel fonctionnel et une architecture permettant à l'utilisateur de modifier l'API "facilement".

La version `former_version` contient une trace de l'API telle qu'elle était au midi du 19/10 (même architecture, sans "l'optimisation" des classes services) et la version `single_endpoint` implémente l'API en utilisant `/api/sort/` en guise de seul endpoint.
## Spécifications techniques

La totalité de l'API a été réalisée en **PHP 7.3**.

**Utilisés pendant le développement :**

- Client API: **Insomnia & Postman**
- IDE: **JetBrains PHPStorm**
- Serveur web: **XAMPP**

## Installation

L'API est téléchargeable par le rendu moodle, ou en tapant dans un terminal:

    git clone https://github.com/RathGate/corbel_b2_php_tp1.git

Il revient à l'utilisateur de placer le contenu du dossier du projet dans le dossier du serveur web utilisé (par exemple le dossier `www` pour WAMP ou `htdocs` pour XAMPP).

A partir de là, l'API devrait être accessible par le biais du `localhost` aux ports configurés par le serveur web.

## Utilisation

Les trois endpoints implémentés nativement trient et retournent une liste d'éléments fournie en paramètre.

#### HTTP Request
    GET http://localhost[...]/api/sort/bubblesort/
    GET http://localhost[...]/api/sort/insertionsort/
    GET http://localhost[...]/api/sort/quicksort/

#### Query Parameters
| Paramètre  | Requis | Type  | Description                                |
|------------|--------|-------|--------------------------------------------|
| arr        | oui    | Array | Contient le tableau à trier au format JSON |

*Exemple:* `http://localhost/corbel_b2_php_tp1/api/sort/insertionsort/?arr=[-1,7,8,5]`

#### Response
Retourne les éléments sous le format JSON suivant :

    {
	    "data": {
		    "sort_function": 
		    "sorted_arr": []
	    }
	}

Dans le cas d'une erreur, la réponse est au format suivant:

    {
	    "error": {
		    "code": // 404, par exemple
		    "message":
		}
	}