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

class PhpHtmlBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testTagCreation()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test()->end()->testTest()->end()->tag('!Test')->end();
        $this->assertEquals('<test></test><test-test></test-test><!Test></!Test>', $builder->build());
    }

    public function testSetAttributes()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test()->setClass('Test')->setTestTest('test')->setTest()->end();
        $this->assertEquals('<test class="Test" test-test="test" test></test>', $builder->build());
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetAttributesException()
    {
        $builder = new PhpHtmlBuilder();
        $builder->setTest();
    }

    public function testAttributesAsArguments()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test(array('test', 'Test' => 'Test'))->end();
        $this->assertEquals('<test test Test="Test"></test>', $builder->build());

        $builder = new PhpHtmlBuilder();
        $builder->tag('test', array('test', 'Test' => 'Test'))->end();
        $this->assertEquals('<test test Test="Test"></test>', $builder->build());
    }

    public function testContentWithAttributesAsArguments()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test('test', array('test', 'Test' => 'Test'))->end();
        $this->assertEquals('<test test Test="Test">test</test>', $builder->build());

        $builder = new PhpHtmlBuilder();
        $builder->tag('test', 'test', array('test', 'Test' => 'Test'))->end();
        $this->assertEquals('<test test Test="Test">test</test>', $builder->build());
    }

    public function testAddComment()
    {
        $builder = new PhpHtmlBuilder();
        $builder->addComment('Test');
        $this->assertEquals('<!--Test-->', $builder->build());
    }

    public function testAddText()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test()->addText('<b>test</b>')->end();
        $this->assertEquals('<test>&lt;b&gt;test&lt;/b&gt;</test>', $builder->build());
    }

    public function testAddHtml()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test()->addHtml('<b>test</b>')->end();
        $this->assertEquals('<test><b>test</b></test>', $builder->build());
    }

    public function testEnd()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test()->end();
        $this->assertEquals('<test></test>', $builder->build());
    }

    public function testEndOpened()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test()->endOpened();
        $this->assertEquals('<test>', $builder->build());
    }

    public function testEndShorted()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test()->endShorted();
        $this->assertEquals('<test />', $builder->build());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAbnormalEnd()
    {
        $builder = new PhpHtmlBuilder();
        $builder->end();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAbnormalEndOpened()
    {
        $builder = new PhpHtmlBuilder();
        $builder->endOpened();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAbnormalEndShorted()
    {
        $builder = new PhpHtmlBuilder();
        $builder->endShorted();
    }

    public function testHierarchy()
    {
        $builder = new PhpHtmlBuilder();
        $builder
            ->test()->end()
            ->test()
                ->test()
                    ->test()->end()
                ->end()
                ->test()->end()
            ->end();
        $this->assertEquals('<test></test><test><test><test></test></test><test></test></test>', $builder->build());
    }

    public function testBuilderAsString()
    {
        $builder = new PhpHtmlBuilder();
        $builder->test()->end();
        $this->assertEquals('<test></test>', (string)$builder);
    }
}