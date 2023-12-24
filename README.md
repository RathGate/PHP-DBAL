
# A propos

Cet exercice propose une implémentation orientée service d'une API proposant d'altérer de manière basique les enregistrements d'une table donnée d'une base de données. Il s'agit d'une version améliorée de l'[API du TP1](https://github.com/RathGate/corbel_b2_php_tp1) .

Dans cette optique, les routes <u>associées au microservice de tri de tableau existent toujours</u> avec les mêmes paramètres, laissées dans le cadre de l'exercice à des fins de témoignage des changements survenus entre le TP1 et le TP2.

Cette API est généraliste et se veut dynamique : les routes concernant les tables fonctionnent quelle que soit la base de donnée renseignée.

## Spécifications techniques 

La totalité de l'API a été réalisée en **PHP 7.3**.

**Utilisés pendant le développement :**

- Client API: **Insomnia & Postman**
- IDE: **JetBrains PHPStorm**
- Serveur web: **XAMPP**

# Installation 

Pour récupérer le projet depuis GitHub :
```
git clone https://github.com/RathGate/corbel_b2_php_tp2.git
```

Ce projet est divisé en deux dossiers : `/www` et `/credentials` :
- le contenu du dossier `/www` doit se trouver dans le dossier du serveur web,
- le dossier `/credentials` doit se trouver un niveau au-dessus du dossier du serveur web à des fins de sécurité.

**Note**: Par défaut, le programme va chercher les propriétés de la base de données à utiliser dans le fichier `/credentials/database.json`. Il revient à l'utilisateur de modifier ce fichier avant de tester le bon fonctionnement de l'API.

# Utilisation

## Notes sur le format 

1. **Position des paramètres de la requête** :
	- Les méthodes  `GET`/`DELETE` n'utiliseront que les paramètres et valeurs fournis dans l'URL de la requête (`query parameter`)
	- Les méthodes `POST`/`PUT`/`PATCH` n'utiliseront que les paramètres et valeurs fournis dans le `body` de la requête, celui-ci au format `x-www-url-form-encoded`.

2. **Format des paramètres de la requête** :
	- Les valeurs des paramètres doivent être notées **sous format JSON**.
**Exemple** :
```
?table="user" ...
?columns=["username", "age", "email"] ...
?values={"username":"rathgate","email":"marianne.corbel@ynov.com"} ...
?where=[["age","BETWEEN",18,25],"OR",["username","LIKE","%Gate"]] ...
```
### Concernant WHERE

Dans un premier temps, un exemple :

```where=[["id","=","10"],"OR",[["username","LIKE","%Gate"],"AND",["age","BETWEEN",15,25]]]```

La requête ci-dessus se traduit en :

```WHERE id = 10 OR (username LIKE "%Gate" AND age BETWEEN 15 AND 25)```

_____

Plusieurs choses à noter :
1. Un seul paramètre WHERE est fourni dans l'URL de la requête, contenant un array au minimum constitué d'une aggrégation ou d'une comparaison - `["id", "=", "12]` ou `[[...], "AND", [...]]`
2. Ce format permet de gérer des conditions imbriquées "facilement" et de manière lisible en économisant un maximum de caractères.
3. Puisque le format paraît assez libre, le code gérant la traduction tableau > SQL réalise beaucoup de vérifications sur les types et valeurs présents dans le/les tableaux.

_____

**Opérateurs supportés**:
- `["=", "<>", "!=", "<", ">", "<=", ">=", "LIKE"]` 
	- Attend trois valeurs (val1, opérateur, val2).
 	- val1 & val2 de type str|number.
- `["IN"]` 
	- Attend trois valeurs (val1, opérateur, val2).
   	- val1 de type str|number, val2 de type array[str|number] 
- `["BETWEEN"]`
  	- Attend quatre valeur (val1, opérateur, val2, val3)
  	- valN de type str|number.
- `["IS NULL", "IS NOT NULL"]` 
	- Attend 2 valeurs (val1, opérateur)
- `["AND", "OR"]`
  	- Attend trois valeurs (val1, opérateur, val2)
  	- val1 & val2 de type array[array|str|number
  
## Endpoints

### LEGACY : Tri de tableau

Le fonctionnement étant toujours le même, vous pouvez retrouver les routes utilisables et leurs paramètres [ici](https://github.com/RathGate/corbel_b2_php_tp1) (l'ancienne documentation pour ne pas prendre trop de place dans celle-ci).

### Modification des données d'une BDD

**Sélectionner des enregistrements dans une table (SELECT)**:
<details>
  <summary><code>GET</code> <code><b>/api/database/record/</b></code></summary>

#### Paramètres 

 | nom              |  type     | data type          | description                         |
 |---------------|----------|------------------|------------------------------|
 | `table`               |    requis | string   | nom de la table                   |
 | `columns`    | optionnel | JSON array[string] | colonnes à sélectionner |
 | `where` | optionnel | JSON array | filtres de la requête | 

#### Responses                                                     
Retourne les éléments sous le format JSON suivant :

```
{
    "data": [
        {
            "id": "8",
            "username": "Kuha",
            "email": "kuha@test",
            "age": "24"
        }]
}
```

Dans le cas d'une erreur :
```
{
    "error": {
        "code": 400,
        "message": "Table `usefdsfr` doesn't exist in database `php_example`."
    }
}
```

#### Exemple d'URL

`GET` `api/database/record/?table="user"&where=[["id","=","10"],"OR",[["username","LIKE","%Gate"],"AND",["age","BETWEEN",15,25]]]`

```
{
    "data": [
        {
            "id": "1",
            "username": "RathGate",
            "email": "rathgate@test.com",
            "age": "25"
        },
        {
            "id": "10",
            "username": "boulbi",
            "email": "boulbi@test.com",
            "age": "16"
        }
    ]
}
```

</details>

_______

**Ajouter un nouvel enregistrement dans une table (INSERT)**:
<details>
  <summary><code>POST</code> <code><b>/api/database/record/</b></code> </summary>

#### Paramètres 

 | nom              |  type     | data type          | description                         |
 |---------------|----------|------------------|------------------------------|
 | `table`               |    requis | string   | nom de la table                   |
 | `values`    | requis | JSON array associatif | valeurs à ajouter |

#### Responses                                                     
Retourne les éléments sous le format JSON suivant :

```
{
    "data": {
        "last_inserted_id": "20"
    }
}
```

Dans le cas d'une erreur :
```
{
    "error": {
        "code": 400,
        "message": "Syntax Error: could not parse parameter `".$param."` [expecting JSON format]."
    }
}
```

#### Exemple d'URL

`POST` `?api/database/record/`

`Body` en `x-www-url-encoded`:
```
table:"user"
values:{"username":"bonjour", "email":"bonjour@test.com"}
```

</details>

______

**Modifier des données dans une table (UPDATE)**:
<details>
  <summary><code>PUT</code> <code><b>/api/database/record/</b></code></summary>

#### Paramètres 

 | nom              |  type     | data type          | description                         |
 |---------------|----------|------------------|------------------------------|
 | `table`               |    requis | string   | nom de la table                   |
 | `values`    | requis | JSON array associatif | valeurs à ajouter |
 | `where` | requis | JSON array | filtres de la requête |

#### Responses                                                     
Retourne les éléments sous le format JSON suivant :

```
{
    "data": {
        "last_inserted_id": "20"
    }
}
```

Dans le cas d'une erreur :
```
{
    "error": {
        "code": 400,
        "message": "Syntax Error: could not parse parameter `where` [expecting JSON format]."
    }
}
```

#### Exemple d'URL

`PUT` `?api/database/record/`

`Body` en `x-www-url-encoded`:
```
table:"user"
values:{"username":"bonjour", "email":"bonjour@test.com"}
where:["id","=","1"]
```

</details>

____

**Supprimer des enregistrements d'une table (DELETE)**:
<details>
  <summary><code>DELETE</code> <code><b>/api/database/record/</b></code></summary>

#### Paramètres 

 | nom              |  type     | data type          | description                         |
 |---------------|----------|------------------|------------------------------|
 | `table`               |    requis | string   | nom de la table                   |
 | `where`    | requis | JSON array | filtres de la requête |

#### Responses                                                     
Retourne les éléments sous le format JSON suivant :

```
{
    "data": {
        "rows_affected": "2"
    }
}
```

Dans le cas d'une erreur :
```
{
    "error": {
        "code": [HTTP error code],
        "message": [error msg]
    }
}
```

#### Exemple d'URL

`DELETE` `?api/database/record/table="user"&where=["age","IS NULL"]`

</details>
