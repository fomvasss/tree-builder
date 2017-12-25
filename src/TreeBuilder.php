<?php

namespace Fomvasss\TreeBuilder;

/**
 * Class TreeBuilder
 *
 * @package Fomvasss\TreeBuilder
 */
class TreeBuilder
{
    /** @var integer|boolean */
    protected $showDepth = true;

    /** @var array */
    protected $exceptKeys = [];

    /**
     * @param array $item
     * @return array
     */
    protected function transform(array $item)
    {
        return $item;
    }

    /**
     * @param array $items
     * @param null $parentId
     * @return array
     */
    public function getTree(array $items, $parentId = null)
    {
        return $this->buildTreeUp($items, $parentId);
    }

    /**
     * @param array $item
     * @return array
     */
    public function getItem(array $item)
    {
        return $this->buildItem($item);
    }

    /**
     * @param array $items
     * @return array
     */
    public function getItems(array $items)
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = $this->buildItem($item);
        }

        return $result;
    }

    /**
     * @return $this
     */
    public function hideDepth()
    {
        $this->showDepth = false;

        return $this;
    }

    /**
     * @param $keys
     * @return $this
     */
    public function setExceptKeys($keys)
    {
        $this->exceptKeys = is_array($keys) ? $keys : [$keys];

        return $this;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function buildItem(array $item)
    {
        $itemTree = $this->transform($item);

        foreach ($this->exceptKeys as $exceptKey) {
            unset($itemTree[$exceptKey]);
        }

        return $itemTree;
    }

    /**
     * @param array $items
     * @param null $parentId
     * @param int $depth
     * @return array
     */
    protected function buildTreeUp(array $items, $parentId = null, $depth = 1)
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $tree[] = array_merge($this->buildItem($item), [
                    'children' => $this->buildTreeUp($items, $item['id'], $depth+1),
                ], $this->showDepth ? ['depth' => $depth] : []);
            }
        }

        return $tree;
    }
}
