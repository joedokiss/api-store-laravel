<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Store;
use App\Lib\Tree;

class StoresController extends Controller
{
    protected $tree;

    public function __construct()
    {
        $this->tree = new Tree();
    }

    /**
     * (5) view all store branches with all of their children
     */
    public function index()
    {
        $storeNodes = Store::all()->toArray();

        return response()->json(['tree' => $this->tree->buildStoreStructure($storeNodes)], 200);
    }

    /**
     * (1) create a store branch
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => 'The "parent_id" and "store_name" are required'
        ];

        $validator = Validator::make($request->all(), [
            'parent_id' => 'required',
            'store_name' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->customMessages], 404);
        }

        $data = [
            'parent_id' => $request->input('parent_id'),
            'store_name' => $request->input('store_name')
        ];

        $newStore = Store::create($request->all());
        $storeNodes = Store::all()->toArray();

        return response()->json([
            'new_store' => $newStore,
            //'tree' => $this->tree->buildStoreStructure($storeNodes)
        ], 201);
    }

    /**
     * (6) view one specific store branch - with all of its children
     * (7) view one specific store branch - without any children
     */
    public function show(Store $store)
    {
        //get all store nodes
        $storeNodes = Store::all()->toArray();

        //build stores structure in tree
        $tree = $this->tree->buildStoreStructure($storeNodes);

        //get all stores without children
        $storesWithoutChildren = $this->tree->getNodesWithoutChildren($tree);

        //if the given store id is in the list of stores having no children
        if (in_array($store->id, $storesWithoutChildren)) {
            return response()->json([
                'search_result' => 'this store has no children',
                'store_details' => $store
            ], 200);
        }

        $store['children'] = $this->tree->buildSubTree($storeNodes, $store->id);

        return response()->json(['store_and_children' => $store], 200);
    }

    /**
     * (2) update a store branch
     * (4) move a store branch (along with all of its children) to a different store branch
     */
    public function update(Request $request, Store $store)
    {
        //if the requested fields are empty, retain the present values
        $parent_id = $request->parent_id ?: $store->parent_id;
        $store_name = $request->store_name ?: $store->store_name;

        $data = ['parent_id' => $parent_id, 'store_name' => $store_name];

        $store->update($data);

        //(2) update a store branch
        if (!$request->parent_id) {
            return response()->json($store, 200);
        }

        //(4) move a store branch (along with all of its children) to a different store branch
        $storeNodes = Store::all()->toArray();
        $tree = $this->tree->buildStoreStructure($storeNodes);

        return response()->json(['updated_tree' => $this->tree->buildStoreStructure($storeNodes)], 200);
    }

    /**
     * (3) delete a store branch along with all of its children
     */
    public function destroy(Store $store)
    {
        //get all store nodes
        $storeNodes = Store::all()->toArray();

        //build stores structure in tree
        $tree = $this->tree->buildStoreStructure($storeNodes);

        //get all stores without children
        $storesWithoutChildren = $this->tree->getNodesWithoutChildren($tree);

        //if the store has no children, simply delete it
        if (in_array($store->id, $storesWithoutChildren)) {
            $store->delete();
        }else{
            $subTree = $this->tree->buildSubTree($storeNodes, $store->id);
            $storeIDs = $this->tree->getAllChildren($subTree);

            array_push($storeIDs, $store->id);

            Store::destroy($storeIDs);
        }

        $storeNodes = Store::all()->toArray();

        //for demonstration purpose, return the updated tree other than 204
        return response()->json(['updated_tree' => $this->tree->buildStoreStructure($storeNodes)], 200);
    }

}
