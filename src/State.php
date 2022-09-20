<?php

namespace Cerbero\JsonParser;

/**
 * The JSON parsing state.
 *
 */
class State
{
    /**
     * The JSON tree.
     *
     * @var Tree
     */
    public Tree $tree;

    /**
     * Whether the tree changed.
     *
     * @var bool
     */
    public bool $treeChanged = false;

    /**
     * The JSON buffer.
     *
     * @var string
     */
    public string $buffer = '';

    /**
     * Whether the token should be an object key.
     *
     * @var bool
     */
    public bool $expectsKey = false;

    /**
     * Instantiate the class.
     *
     */
    public function __construct()
    {
        $this->tree = new Tree();
    }
}
