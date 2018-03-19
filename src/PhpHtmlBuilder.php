<?php

/*
 * This file is part of the PhpHtmlBuilder package.
 *
 * (c) Andrew Polupanov <andrewfortalking@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AvpLab;

use AvpLab\Element\Comment;
use AvpLab\Element\Element;
use AvpLab\Element\Text;
use AvpLab\Element\Tag;

/**
 * Provides API for easy building of HTML code in php
 */
class PhpHtmlBuilder
{
    /**
     * @var Element[]
     */
    private $elements = array();

    /**
     * @var \stdClass
     */
    private $scope;

    /**
     * @param string $method
     * @param array $arguments
     * @return $this
     * @throws \LogicException When element is not initialized yet
     */
    public function __call($method, $arguments)
    {
        $tagName = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $method));
        if (stripos($tagName, 'set-') === 0) {
            if ($this->scope === null) {
                throw new \LogicException('Attributes can be set for elements only');
            }
            $this->scope->attributes[substr($tagName, 4)] = isset($arguments[0]) ? $arguments[0] : null;
        } else {
            $this->createScope($tagName, $arguments);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    private function createScope($name, $arguments)
    {
        // Create new scope
        $scope = new \stdClass();
        $scope->name = $name;
        $scope->attributes = array();
        $scope->elements = array();
        $scope->parent = $this->scope;

        // resolve arguments
        if ($arguments) {
            if (is_array($arguments[0])) {
                $scope->attributes = $arguments[0];
            } else {
                $scope->elements[] = new Text($arguments[0], false);
                if (isset($arguments[1]) && is_array($arguments[1])) {
                    $scope->attributes = $arguments[1];
                }
            }
        }

        $this->scope = $scope;
    }

    private function addElementToScope(Element $element)
    {
        if ($this->scope) {
            $this->scope->elements[] = $element;
        } else {
            $this->elements[] = $element;
        }
    }

    /**
     * @param string $name
     * @return $this
     */
    public function tag($name)
    {
        $arguments = func_get_args();
        array_shift($arguments);
        $this->createScope($name, $arguments);
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function addText($text)
    {
        $element = new Text($text);
        $this->addElementToScope($element);
        return $this;
    }

    /**
     * @param string $html
     * @return $this
     */
    public function addHtml($html)
    {
        $element = new Text($html, false);
        $this->addElementToScope($element);
        return $this;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function addComment($comment)
    {
        $element = new Comment($comment);
        $this->addElementToScope($element);
        return $this;
    }

    /**
     * @return $this
     * @throws \RuntimeException When element is not initialized yet.
     */
    public function end()
    {
        if ($this->scope === null) {
            throw new \RuntimeException('Abnormal element completion');
        }
        $element = new Tag($this->scope->name, $this->scope->attributes, $this->scope->elements);
        $this->scope = $this->scope->parent;
        $this->addElementToScope($element);
        return $this;
    }

    /**
     * @return $this
     * @throws \RuntimeException When element is not initialized yet.
     */
    public function endShorted()
    {
        if ($this->scope === null) {
            throw new \RuntimeException('Abnormal element completion');
        }
        $element = new Tag($this->scope->name, $this->scope->attributes);
        $element->setShort(true);
        $this->scope = $this->scope->parent;
        $this->addElementToScope($element);
        return $this;
    }

    /**
     * @return $this
     * @throws \RuntimeException When element is not initialized yet.
     */
    public function endOpened()
    {
        if ($this->scope === null) {
            throw new \RuntimeException('Abnormal element completion');
        }
        $element = new Tag($this->scope->name, $this->scope->attributes);
        $element->setOpened(true);
        $this->scope = $this->scope->parent;
        $this->addElementToScope($element);
        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        $result = '';
        foreach ($this->elements as $element) {
            $result .= $element;
        }
        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
}