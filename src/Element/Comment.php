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
 * Provides html comment block
 */
class Comment extends Element
{
    /**
     * @var string
     */
    private $comment;

    /**
     * @param string $comment
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * {@inheritdoc}
     */
    protected function render()
    {
        return sprintf('<!--%s-->', htmlspecialchars($this->comment, ENT_COMPAT));
    }
}