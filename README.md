
## Notes sur le format 

1. **Position des paramètres de la requête** :
	- Les méthodes  `GET`/`DELETE` n'utiliseront que les paramètres et valeurs fournis dans l'URL de la requête (`query parameter`)
	- Les méthodes `POST`/`PUT`/`PATCH` n'utiliseront que les paramètres et valeurs fournis dans le `body` de la requête, celui-ci au format `x-www-url-form-encoded`.

A noter que l'intérêt du format `x-www-url-form-encoded` réside dans le fait que le format des paramètres restera le même (qu'ils soient fournis en `GET` ou en `POST`). N'ayant pas beaucoup d'expérience sur la question, j'ai privilégié la simplicité d'utilisation et l'unicité dans le cadre de tests répétés.

2. **Format des paramètres de la requête** :
	- Les valeurs des paramètres doivent être notées **sous format JSON**.
	
**Exemple** :
```
?table="user" ...
?columns=["username", "age", "email"] ...
?values={"username":"rathgate","email":"marianne.corbel@ynov.com"} ...
?where=[["age","BETWEEN",18,25],"OR",["username","LIKE","%Gate"]] ...
```


## Endpoints

### LEGACY : Tri de tableau

Le tri de tableau proposé lors du TP 1 existe toujours ici ! 

**Note** : Evidemment, ce n'est pas un élément... essentiel d'une API cherchant à altérer une base de données. Mais l'*endpoint* pouvant se supprimer en deux clics, je l'ai laissé en guise de témoin des changements de la structure de l'API entre les deux TP.

____

<details>
  <summary><code>GET</code> <code><b>/api/sort/{algorithme}</b></code> <code>(trie un tableau avec l'algorithme choisi)</code></summary>

#### Algorithmes nativement implémentés :
- bubblesort
- insertionsort
- quicksort

#### Paramètres 

 | nom              |  type     | data type          | description                         |
 |---------------|----------|------------------|------------------------------|
 | `arr`               |    requis | tableau JSON   | Tableau à trier                     |

#### Responses                                                     
Retourne les éléments sous le format JSON suivant :
```
{
    "data": {
	    "sort_function": 
	    "sorted_arr": []
    }
}
```
Dans le cas d'une erreur :
```
{
    "error": {
	    "code": // 404, par exemple
	    "message":
	}
}
```

#### Exemple d'URL

`GET` `/api/sort/insertionsort/?arr=[-1,7,8,5]`

</details>

### Modification des données d'une BDD

