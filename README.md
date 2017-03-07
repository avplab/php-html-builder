# Php Html Builder

[![Build Status](https://travis-ci.org/avplab/php-html-builder.svg?branch=master)](https://travis-ci.org/avplab/php-html-builder)


Sometimes, we strongly need something simple for creating html code in php runtime. For example, 
we want to build a simple html report, or build a simple html for highlighting some profiling data, etc.
Usually for such cases we don't want to use templates engines, or create separated html files. 
As result we have mess of html and php code, or tons of concatenated strings with html code.
PhpHtmlBuilder was created to solve these issues quickly and without clogging the php code. You simply use it same as you write a html.

## Installation
Install the component by using [Composer](https://getcomposer.org). Update your project's `composer.json` file to include dependency.

    "require": {
        "avplab/php-html-builder": "~2.0"
    }

## Usage

To start building an html code, create an instance of `AvpLab\PhpHtmlBuilder` and use it similary as writing html.
```php

$builder = new \AvpLab\PhpHtmlBuilder();
$builder
    ->tag('!DOCTYPE')->setHtml()->endOpened()
    ->html()->setLang('en')
        ->head()
            ->meta()->setHttpEquiv('X-UA-Compatible')->setContent('IE=edge,chrome=1')->endOpened()
            ->title('PhpHtmlBuilder: Example')->end()
        ->end()
        ->body()
            ->div()->setClass('container')
                ->div()->setClass('row')
                    ->div()->setClass('col-md-12')
                        ->h1('PhpHtmlBuilder Demo')->end()
                        ->p('Designed to make the code easier')->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ->end();

echo $builder;
```
### Comments
To add comment block use method `addComment()`.
```
$builder = new \AvpLab\PhpHtmlBuilder();
echo $builder->addComment('foo');

//Result
<!--foo-->
```
### Tags
There are two ways to create HTML tags:
The first one(this is also the most common way) is to call the method with the same name as HTML tag in `CamelCase` format.
Tags are always be converted into lowercase with dashes.
```php
$builder = new \AvpLab\PhpHtmlBuilder();
echo $builder->html()->customTag()->end()->end();

//Result
<html><custom-tag></custom-tag></html>
```

The second way is to call the method `tag()` with the name of the HTML tag. In this case no any conversion is applied.
This is useful when you need to create very specific tags like `<!DOCTYPE>`.
```php
$builder = new \AvpLab\PhpHtmlBuilder();
echo $builder->tag('!DOCTYPE')->endOpened();

//Result
<!DOCTYPE>
```
To complete the tag need to call one of the following methods: `end()`, `endShorted()` and `endOpened()`.
* Method `end()` will create tag `<tag></tag>` (i.e. `div`, `p`, `span`, etc.).
* Method `endShorted()` will create short tag `<tag />`. In this case, tag can have only attributes (i.e. `script`, `link`, `img` or `input` and similar).
* Method `endOpened()` will create opened tag `<tag>`. In this case, tag can have only attributes (i.e. `meta` or similar).

There is also a possibility to add html and attributes during tag creation, using arguments of methods.
Arguments will be recognized in the following way:
- If only one argument is provided, and this is an array, it will be recognized as tag attributes, otherwise as tag html(see `addHtml()` below).
- If two arguments are provided, the first is a tag html, and second is array of attributes.
NOTE: in this case don't need to use camelCase for attributes names. It will keep the name as is. Also if attribute doesn't have a key, it will be recognized as attribute without value.
```php
$builder = new \AvpLab\PhpHtmlBuilder();
echo $builder
    ->div('<h1>title</h1>', ['class' => 'container', 'Foo' => 'Bar', 'baz'])
        ->p(['class' => 'article'])->end()
    ->end();

//Result
<div class="container" Foo="Bar" baz><h1>title</h1><p class="article"></p></div>
```

#### Attributes
Creating attributes is very similar as creating tags. You need to call the method with an appropriate name in `CamelCase` format and beginning with `set`.
If method called without arguments, only attribute name will be applied.
```php
$builder = new \AvpLab\PhpHtmlBuilder();
$builder
    ->tag('!DOCTYPE')->setHtml()->endOpened()
    ->html()->setLang('en')->end();

//Result
<!DOCTYPE html><html lang="en"></html>
```

#### Content
To add a plain text(escaped) into the tag, call the method `addText()`. For adding "raw" html string, use another method `addHtml()`.
```php
$builder = new \AvpLab\PhpHtmlBuilder();
$builder
    ->div()
        ->addText('foo')
        ->addHtml('<b>bar</b>')
    ->end();

//Result
<div>foo<b>bar</b></div>
```

## Render

To get an HTML string, you need to call the `build()` method or simply recognize it as string.
```php
$builder = new \AvpLab\PhpHtmlBuilder();
// Do some html

$htmlString = $builder->build();
echo $htmlString;

// Supports string recognision
echo $builder;
```

# License

PhpHtmlBuilder is licensed under the MIT License - see the `LICENSE` file for details
