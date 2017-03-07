<?php

/*
 * This file is part of the PhpHtmlBuilder package.
 *
 * (c) Andrew Polupanov <andrewfortalking@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AvpLab\Element;

/**
 * Provides text block. Supports escaping.
 */
class Text extends Element
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var bool
     */
    private $isPlain;

    /**
     * @param string $text
     * @param bool $isPlain
     */
    public function __construct($text, $isPlain = true)
    {
        $this->text = $text;
        $this->isPlain = $isPlain;
    }

    protected function render()
    {
        return $this->isPlain ? htmlspecialchars($this->text, ENT_COMPAT) : (string)$this->text;
    }
}