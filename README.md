
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
2. Ce format permet de gérer des conditions imbriquées "facilement" en économisant un maximum de caractères.
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

Le tri de tableau proposé lors du TP 1 existe toujours ici ! 

**Note** : Evidemment, ce n'est pas un élément... essentiel d'une API cherchant à altérer une base de données. Mais l'*endpoint* pouvant se supprimer en deux clics, je l'ai laissé en guise de témoin des changements de la structure de l'API entre les deux TP.

Rappel des endpoints TODO

### Modification des données d'une BDD

**Sélectionner des données dans la base (SELECT)**:
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
**Ajouter des données dans la base (INSERT)****:
<details>
  <summary><code>GET</code> <code><b>/api/database/record/</b></code> <code>(ajoute **une** nouvelle entrée à la base de données)</code></summary>

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

