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

class PhpHtmlBuilder
{
    /**
     * @var int
     */
    private $currentTag;

    /**
     * @var array
     */
    private $tags = array();

    /**
     * @var array
     */
    private $rootTags = array();

    /**
     * @var array
     */
    private $tagsStack = array();

    /**
     * Creates new tag with specific name
     *
     * @param string $name
     * @return $this
     */
    public function tag($name)
    {
        return $this->__call($name);
    }

    /**
     * Appends custom content to the tag's content
     *
     * @param string $content
     * @return $this
     */
    public function append($content)
    {
        $this->appendContent($content);
        return $this;
    }

    /**
     * Prepends custom content to the tag's content
     *
     * @param string $content
     * @return $this
     */
    public function prepend($content)
    {
        $this->prependContent($content);
        return $this;
    }

    /**
     * Close current tag and returns to the parent tag
     *
     * @return $this
     */
    public function end()
    {
        array_pop($this->tagsStack);
        $this->currentTag = end($this->tagsStack);
        if ($this->currentTag === false) {
            unset($this->currentTag);
        }
        return $this;
    }

    /**
     * Close current tag as shorted and return to the parent tag.
     * (The tag will be closed with ending slash in opened tag. The closed tag won't be rendered)
     *
     * @return $this
     */
    public function endShorted()
    {
        $this->tags[$this->currentTag]['isShorted'] = true;
        return $this->end();
    }

    /**
     * Close current tag as opened.
     * (The opened tag will be closed and closed tag won't be rendered)
     *
     * @return $this
     */
    public function endOpened()
    {
        $this->tags[$this->currentTag]['isOpened'] = true;
        return $this->end();
    }

    /**
     * Process calls
     *
     * @param string $tagName
     * @param string[] $arguments (Optional)
     * @return $this
     */
    public function __call($tagName, $arguments = null)
    {
        // Attributes detected
        if (strpos($tagName, 'set') === 0) {
            // Transform camelCase to camel-case
            $attribute = preg_replace('/(?<!^)([A-Z])/', '-$1', substr($tagName, 3));
            $attributeValue = isset($arguments[0]) ? $arguments[0] : null;
            $this->setAttributes(strtolower($attribute), $attributeValue);
        } else {
            // Create new tag
            $tagIndex = $this->createTag($tagName);
            if (empty($this->tagsStack)) {
                $this->rootTags[] = $tagIndex;
            }
            if (isset($this->currentTag)) {
                $this->addChild($tagIndex);
            }
            $this->currentTag = $tagIndex;
            array_push($this->tagsStack, $tagIndex);

            // Append a content if it is presented
            if (isset($arguments[0])) {
                $this->appendContent($arguments[0]);
            }
        }
        return $this;
    }

    /**
     * Build html string
     *
     * @return string
     */
    public function build()
    {
        $html = '';
        foreach ($this->rootTags as $tagIndex) {
            $html .= $this->renderTag($tagIndex);
        }
        return $html;
    }

    /**
     * Represent builder as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * Create tag and put it to the storage
     *
     * @param string $name
     * @return int
     */
    private function createTag($name)
    {
        $this->tags[] = array(
            'name' => $name,
            'attributes' => array(),
            'isShorted' => false,
            'isOpened' => false,
            'appendContent' => null,
            'prependContent' => null,
            'children' => array()
        );
        return count($this->tags) - 1;
    }

    /**
     * @param string $content
     */
    private function appendContent($content)
    {
        $this->tags[$this->currentTag]['appendContent'] .= $content;
    }

    /**
     * @param string $content
     */
    private function prependContent($content)
    {
        $this->tags[$this->currentTag]['prependContent'] .= $content;
    }

    /**
     * @param int $tagIndex
     */
    private function addChild($tagIndex)
    {
        $this->tags[$this->currentTag]['children'][] = $tagIndex;
    }

    /**
     * @param string|array $name
     * @param string $value (Optional)
     * @return $this
     */
    private function setAttributes($name, $value = null)
    {
        if (is_array($name)) {
            $this->tags[$this->currentTag]['attributes'] = array_merge($this->tags[$this->currentTag]['attributes'], $name);
        } else {
            $this->tags[$this->currentTag]['attributes'][$name] = $value;
        }
    }

    /**
     * @param int $tagIndex
     * @return string
     */
    private function renderTag($tagIndex)
    {
        $tag = $this->tags[$tagIndex];

        if ($tag['isShorted']) {
            return $this->renderShort($tag);
        } elseif ($tag['isOpened']) {
            return $this->renderOpened($tag);
        } else {
            $html = $this->renderOpened($tag);
            if ($tag['prependContent']) {
                $html .= $tag['prependContent'];
            }
            if ($tag['children']) {
                foreach ($tag['children'] as $childIndex) {
                    $html .= $this->renderTag($childIndex);
                }
            }
            if ($tag['appendContent']) {
                $html .= $tag['appendContent'];
            }
            $html .= $this->renderClosed($tag);
            return $html;
        }
    }

    /**
     * @param array $tag
     * @return string
     */
    private function renderOpened(array $tag)
    {
        return sprintf('<%s%s>', $tag['name'], $this->renderAttributes($tag));
    }

    /**
     * @param array $tag
     * @return string
     */
    private function renderClosed(array $tag)
    {
        return sprintf('</%s>', $tag['name']);
    }

    /**
     * @param array $tag
     * @return string
     */
    private function renderShort(array $tag)
    {
        return sprintf('<%s%s />', $tag['name'], $this->renderAttributes($tag));
    }

    /**
     * @param array $tag
     * @return string
     */
    private function renderAttributes(array $tag)
    {
        $attributes = '';
        foreach ($tag['attributes'] as $attr => $val) {
            $attributes .= ' ' . $attr;
            if ($val !== null) {
                $val = htmlspecialchars($val, ENT_COMPAT);
                $attributes .= '="' . $val . '"';
            }
        }
        return $attributes;
    }
}