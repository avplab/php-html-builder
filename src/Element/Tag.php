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
 * Provides html Tag. Supports opened and short tags
 */
class Tag extends Element
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string[]
     */
    private $attributes = array();

    /**
     * @var bool
     */
    private $isShort = false;

    /**
     * @var bool
     */
    private $isOpened = false;

    /**
     * @var Element[]
     */
    private $children = array();

    /**
     * @param string $name
     * @param string[] $attributes
     * @param Element[] $children
     */
    public function __construct($name, array $attributes = array(), array $children = array())
    {
        $this->name = $name;
        $this->setAttributes($attributes);
        $this->setChildren($children);
    }

    /**
     * @param string[] $attributes
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            if (is_numeric($name)) {
                $this->attributes[$value] = null;
            } else {
                $this->attributes[$name] = $value;
            }
        }
    }

    /**
     * @param Element[] $children
     */
    public function setChildren(array $children)
    {
        $this->children = array_merge($this->children, $children);
    }

    /**
     * @param bool $isShort
     */
    public function setShort($isShort)
    {
        $this->isShort = $isShort;
    }

    /**
     * @param bool $isOpened
     */
    public function setOpened($isOpened)
    {
        $this->isOpened = $isOpened;
    }

    /**
     * [@inheritdoc}
     */
    protected function render()
    {
        if ($this->isShort) {
            return $this->renderShort();
        } elseif ($this->isOpened) {
            return $this->renderOpened();
        } else {
            return $this->renderTag();
        }
    }

    /**
     * @return string
     */
    private function renderAttributes()
    {
        $result = '';
        if ($this->attributes) {
            $attributes = array();
            foreach ($this->attributes as $name => $value) {
                if ($value === null) {
                    $attributes[] = $this->escape($name);
                } else {
                    $attributes[] = sprintf(
                        '%s="%s"', $this->escape($name), $this->escape($value)
                    );
                }
            }
            $result = ' ' . implode(' ', $attributes);
        }
        return $result;
    }

    /**
     * @return string
     */
    private function renderShort()
    {
        return sprintf('<%s%s />', $this->name, $this->renderAttributes());
    }

    /**
     * @return string
     */
    private function renderOpened()
    {
        return sprintf('<%s%s>', $this->name, $this->renderAttributes());
    }

    /**
     * @return string
     */
    private function renderTag()
    {
        $children = '';
        foreach ($this->children as $child) {
            $children .= $child;
        }
        return sprintf('%s%s</%s>', $this->renderOpened(), $children, $this->name);
    }

    /**
     * Helper for escaping
     *
     * @param $value
     * @return string
     */
    private function escape($value)
    {
        return htmlspecialchars($value, ENT_COMPAT);
    }
}