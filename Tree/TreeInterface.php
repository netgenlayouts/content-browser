<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tree;

interface TreeInterface
{
    /**
     * Returns the configured adapter.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface
     */
    public function getAdapter();

    /**
     * Returns the tree config.
     *
     * @return array
     */
    public function getConfig();

    /**
     * Returns all root items.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Item[]
     */
    public function getRootItems();

    /**
     * Loads the item for specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException If item is outside of root items
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Item
     */
    public function getItem($itemId);

    /**
     * Loads all children of the specified item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Item[]
     */
    public function getChildren(Item $item);

    /**
     * Returns if current item has children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return bool
     */
    public function hasChildren(Item $item);

    /**
     * Loads all categories below the specified item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Item[]
     */
    public function getSubCategories(Item $item);

    /**
     * Returns if current item has child categories.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return bool
     */
    public function hasSubCategories(Item $item);

    /**
     * Returns if provided item is one of the root items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return bool
     */
    public function isRootItem(Item $item);

    /**
     * Returns if provided item is inside one of the root items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return bool
     */
    public function isInsideRootItems(Item $item);
}