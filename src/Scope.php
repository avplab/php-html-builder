<?php

/*
 * This file is part of the PhpHtmlBuilder package.
 *
 * (c) Thierry Feuzeu <thierry.feuzeu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AvpLab;

use AvpLab\Element\Text;

use function is_string;
use function is_array;

class Scope
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var Scope|null
     */
    public $parent;

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var array
     */
    public $elements = [];

    /**
     * Custom data to extend the scope
     *
     * @var mixed
     */
    public $extension = null;

    /**
     * The constructor
     *
     * @param string $name
     * @param array $arguments
     * @param Scope|null $parent
     */
    public function __construct($name, array $arguments = [], $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        // Resolve arguments
        foreach ($arguments as $argument) {
            if (is_string($argument)) {
                $this->elements[] = new Text($argument, false);
            } elseif (is_array($argument)) {
                $this->attributes = $argument;
            }
        }
    }
}
