## 1. About this API:
> this Restful API is built by Laravel 5.5

> the tree structure (see snapshot 1 as example, which is used in this demo application) will be essentially demonstrated as the array structure (eg. array $stores), and the tree will be built as (see snapshot 2 as example)
```
$stores = [
	['id' => 1,  'parent_id' => 0, 'store_name' => 'A'],
	['id' => 2,  'parent_id' => 0, 'store_name' => 'B'],
	['id' => 3,  'parent_id' => 0, 'store_name' => 'C'],
	['id' => 4,  'parent_id' => 1, 'store_name' => 'D'],
	['id' => 5,  'parent_id' => 1, 'store_name' => 'E'],
	['id' => 6,  'parent_id' => 2, 'store_name' => 'F'],
	['id' => 7,  'parent_id' => 2, 'store_name' => 'G'],
	['id' => 8,  'parent_id' => 2, 'store_name' => 'H'],
	['id' => 9,  'parent_id' => 3, 'store_name' => 'I'],
	['id' => 10, 'parent_id' => 9, 'store_name' => 'J'],
	['id' => 11, 'parent_id' => 9, 'store_name' => 'K'],
	['id' => 12, 'parent_id' => 7, 'store_name' => 'L']
];
```
###### snapshot 1
![tree_structure](https://user-images.githubusercontent.com/39091872/51833525-ce2bb100-234b-11e9-89b5-67959f8c53ed.png)

###### snapshot 2

* Each element in the $stores array stands for a store branch, and has a 'parent_id' indicating store's parent
* Each element in the $stores array is saved into the database as a row.
* after the tree is built, each element will have a key "children" to store the children (if any).

## 2. About scripts:
###### Routing:
The routes are defined in the /routes/api.php, all actions in this case would need to be authenticated.

###### Models:
The models are defined in the /app/Models, in most cases, the API is leveraging the Laravel native Eloquent (ORM), and therefore, you may not see explicitly defined functions in models.

The method "generateToken()" in the User model is used to generate the authentication token to access the API, essentially the token will be refreshed after each login.
###### Controllers:
There are three main controllers involved in this case
- /app/Http/Controllers/Auth/LoginController.php
- /app/Http/Controllers/Auth/RegisterController.php
- /app/Http/Controllers/StoresController.php
it will take care of the routed HTTP actions according to URI

## 3. How the demo works:
###### missions:
/**
 * (1) create a store branch
 * (2) update a store branch
 * (3) delete a store branch along with all of its children
 * (4) move a store branch (along with all of its children) to a different store branch
 * (5) view all store branches with all of their children
 * (6) view one specific store branch with all of its children
 * (7) view one specific store branch without any children
 */
 
#### (1) create a store branch
```
URI: http://store.test/api/stores
Method: POST
Controller action: store
```
eg. create a new store (M) as child of store A (id => 1) 
```
URL: http://store.test/api/stores
Body: {
  "parent_id":1,
  "store_name":"M",
}
```
#### (2) update a store branch
#### (4) move a store branch (along with all of its children) to a different store branch
###### NOTE:
both (2) and (4) will be managed by PUT (update), the change to "parent_id" will have the certain store along with its children (if any) moved, if the "parent_id" remains the same, the store will not be moved, the other information (like store name) can be updated instead.

```
URI: http://store.test/api/stores/{store}
Method: PUT
Controller action: update
```
eg. it will move the store A (id => 1) and all its children under C (id => 3), and A store name will remain same
```
URL: http://store.test/api/stores/1
Body: {
 "parent_id":3
}
```
eg. it will only update store A's name
```
URI: http://store.test/api/stores/1
Body: {
 "store_name":"A_updated_name"
}
```

#### (3) delete a store branch along with all of its children
```
URI: http://store.test/api/stores/{store}
Method: DELETE
Controller action: destroy
```
eg. it will delete node B (id => 2) and all its children (if any)
```
URI: http://store.test/api/stores/2
```
#### (5) view all store branches with all of their children
```
URI: http://store.test/api/stores
Method: GET
Controller action: index
```
eg. it will list all stores in tree array structure
```
URI: http://store.test/api/stores
```
#### (6) view one specific store branch with all of its children
#### (7) view one specific store branch without any children
```
URI: http://store.test/api/stores/{store}
Method: GET
Controller action: show
```
- if the provided store has any children, the sub-tree (the provided store will be as 'root') will be built and returned
- if the provided store has no children, the API will return store's details

eg. store E (id => 5) has no children, and therefore, this will only return store E's details 
```
URI: http://store.test/api/stores/5
```
eg. store A (id => 1) has children, and therefore, this will return a sub-tree (store A is root)
```
URI: http://store.test/api/stores/1
```

## 4. Notes to unit test:
the last tree test cases are dependent because they were testing the real actions other than leveraging the mocking any more, you may consider to test those one by one (roll back the database to original status with raw data every time) other than at once, but the following test sequence will work anyway.
```
* test_create_node_M_as_A_child
* test_move_A_branch_as_C_child
* test_delete_C_branch_and_children
```
