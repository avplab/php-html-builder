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
        $builder->foo()->end();
        $this->assertEquals('<foo></foo>', $builder->build());
    }

    public function testSpecificTagCreation()
    {
        $builder = new PhpHtmlBuilder();
        $builder->tag('foo')->end();
        $this->assertEquals('<foo></foo>', $builder->build());
    }

    public function testSetAttributes()
    {
        $builder = new PhpHtmlBuilder();
        $builder
            ->foo()
                ->setClass('bar')
                ->setNgDataModel('baz')
                ->setChecked()
            ->end();
        $this->assertEquals('<foo class="bar" ng-data-model="baz" checked></foo>', $builder->build());
    }

    public function testSetContent()
    {
        $builder = new PhpHtmlBuilder();
        $builder
            ->foo()
                ->prepend('bar')
                ->append('baz')
                ->bar()->end()
            ->end();
        $this->assertEquals('<foo>bar<bar></bar>baz</foo>', $builder->build());
    }

    public function testEnd()
    {
        $builder = new PhpHtmlBuilder();
        $builder
            ->foo()->end()
            ->bar()->endShorted()
            ->baz()->endOpened();
        $this->assertEquals('<foo></foo><bar /><baz>', $builder->build());
    }

    public function testFullHtml()
    {
        $builder = new PhpHtmlBuilder();
        $builder
            ->foo()->setChecked()->endOpened()
            ->bar()
                ->setClass('baz')
                ->append('append')
                ->baz()
                    ->foo('test')->end()
                ->end()
                ->br()->endShorted()
            ->end();
        $this->assertEquals('<foo checked><bar class="baz"><baz><foo>test</foo></baz><br />append</bar>', $builder->build());
    }
}