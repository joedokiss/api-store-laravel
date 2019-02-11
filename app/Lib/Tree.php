<?php
namespace App\Lib;

class Tree
{
    /**
     * build the entire tree structure
     * @param  [array] $stores [all tree nodes]
     * @return [array]         [a comprehensive tree structure]
     */
    public function buildStoreStructure($stores)
    {
        $result = [];

        $nodes = array_column($stores, null, 'id');

        foreach ($nodes as $node) {
            if (isset($nodes[$node['parent_id']])) {
                $nodes[$node['parent_id']]['children'][] = &$nodes[$node['id']];

            } else {
                $result[] = &$nodes[$node['id']];
            }
        }

        return $result;
    }

    /**
     * build a sub-tree from the given node
     * @param  [array] $nodes     [all tree nodes]
     * @param  [int]   $parent_id [the given new 'root' for the sub-tree]
     * @return [array]            [a sub-tree]
     */
    public function buildSubTree($nodes, $parent_id)
    {
        $tree = [];

        foreach($nodes as $key => $node)
        {
            if($node['parent_id'] == $parent_id)
            {
                $result = $this->buildSubTree($nodes, $node['id']);

                if (!empty($result)) {
                    $node['children'] = $result;
                }

                $tree[] = $node;
                unset($nodes[$key]);
            }
        }

        return $tree;
    }


    /**
     * get all the nodes without children
     * @param  [array] $tree     [the built tree by nodes]
     * @return [array]           [all the node IDs without children]
     */
    public function getNodesWithoutChildren($tree)
    {
        static $data;

        foreach ($tree as $key => $val ) {
            if (isset($val['children']) && is_array ($val['children'])) {
                $this->getNodesWithoutChildren($val['children']);
            } else {
                //collect nodes without children
                $data[]=$val['id'];
            }
        }
        return $data;
    }

    /**
     * find out all children of the provided store
     * @param  [array] $children [store's children]
     * @return [array]           [all children IDs]
     */
    public function getAllChildren($children)
    {
        static $data;

        foreach ($children as $key => $val ) {

            $data[] = $val['id'];

            if (isset($val['children']) && !empty($val['children']) && is_array ($val['children'])) {
                $this->getAllChildren($val['children']);
            }
        }

        return $data;
    }
}