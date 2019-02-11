## 1. About this API:
> this Restful API is built by Laravel 5.5

> the tree structure (see snapshot 1 as example, which is used in this demo application) will be essentially demonstrated as the array structure (eg. array $nodes), and the tree will be built as (see snapshot 2 as example)
```
$nodes = [
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

* Each element in the $nodes array stands for a store branch, and has a 'parent_id' indicating store's parent
* Each element in the $nodes array is saved into the database as a row.
* after the tree is built, each element will have a key "children" to store the children (if any).

## 2. About scripts:
* Routing:
The routes are defined in the /routes/api.php, all actions in this case would need to be authenticated.
* Models:
The models are defined in the /app/Models, in most cases, the API is leveraging the Laravel native Eloquent (ORM), and therefore, you may not see explicitly defined functions in models.

The method "generateToken()" in the User model is used to generate the authentication token to access the API, essentially the token will be refreshed after each login.

## 3. How the demo works:
###### NOTE:

#### (1) create a store branch
```
URI: /restful/branches
Method: POST
Action: create
```
eg. pass the body parameters like
```
{
  "parent_id":1,
  "store_name":"M",
  "store_state":"VIC"
}
```
#### (2) update a store branch
#### (4) move a store branch (along with all of its children) to a different store branch
###### NOTE:
combine both (2) and (4) in one place, because as long as the node's "parent_id" is changed, 
all of its children will be moved, which can be considered as part of "update", 
in case the "parent_id" remains the same, the node will not be moved, the other information can be updated instead.

```
URI: /restful/branches/{id}
Method: PUT
Action: update
```
eg. this sample will move the node A and all its children under C 
```
/restful/branches/1
{
 "parent_id":3
}
```
eg. this sample will update node A's details
```
/restful/branches/1
{
 "store_name":"AA",
 "store_state":"QLD"
}
```

#### (3) delete a store branch along with all of its children
```
URI: /restful/branches/{id}
Method: DELETE
Action: delete
```
eg. this sample will delete node B and all its children (if any)
```
/restful/branches/3
```
#### (5) view all store branches with all of their children
```
URI: /restful/branches
Method: GET
```
eg. 
```
/restful/branches
```
#### (6) view one specific store branch with all of its children
#### (7) view one specific store branch without any children
```
URI: /restful/branches/{id}
Method: GET
Action: view
```
eg. it will outline the subtree (node B as root including all its children, if any) 
```
/restful/branches/2
{
  "children":"true"
}
```

> NOTE: with/without children can be controlled by passing parameter "children" ("true" is with children, "false" is "without children")

eg. this example tends to view store (node A) and assuming it has no children, in which case, the API will respond an array having all available nodes without the children 
```
/restful/branches/1
{
  "children":"false"
}
```
## 4. Notes to unit test:
the last tree test cases are dependent because they were testing the real actions other than leveraging the mocking any more, you may consider to test those one by one (roll back the database to original status with raw data every time) other than at once, but the following test sequence will work anyway.
```
* test_create_node_M_as_A_child
* test_move_A_branch_as_C_child
* test_delete_C_branch_and_children
```
