# Php Html Builder
Sometimes, we strongly need something simple for creating html code in php runtime. For example, 
we want to build a simple html report, or build a simple html for highlighting some profiling data, etc.
Usually for such cases we don't want to use templates engines, or create separated html files. 
As result we have mess of html and php code, or tons of concatenated strings with html code.
PhpHtmlBuilder was created to solve these issues quickly and without clogging the php code. You simply use it same as you write a html.

## Installation
Install the component by using [Composer](https://getcomposer.org). Update your project's `composer.json` file to include dependency.

    "require": {
        "avplab/php-html-builder": "~1.0"
    }

## Usage

You create an instance of `AvpLab\PhpHtmlBuilder` and use it similary as you write a html.

    <?php
    
    use AvpLab\PhpHtmlBuilder;

    $builder = new PhpHtmlBuilder();
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
    
# API

## Tag creation
For creating tags, you need to call the method with the same name as html tag.
When you need to close the tag and return to the parent, there are three methods to do it: `end`, `endShorted` and `endOpened`.

* The `end` will close the tag as usual `<tag></tag>`. Usually such tags could be as container for inner tags, or tags like `a`, `p`, `span` etc.
* The `endShorted` will close the tag as `<tag />`. In this case the tag doesn't have any content or child tags. This is useful for `br`, `img` tags or even `input` tag.
* The `endOpened` will close the tag as `<tag>`. Other words, it keeps tag opened, but this tag doesn't have any content or child tags. This is useful for `meta` tags or similar.


    $builder
        ->html()
            ->body()
                ...
            ->end()
        ->end();
        
    //Result
    <html><body> ... </body></html>
    
### Specific tags
For creating a tag with specific name(e.g. `<!DOCTYPE html>`), you need to call the method `tag`.

    $builder
        ->tag('!DOCTYPE')->endOpened()
        
    //Result
    <!DOCTYPE>
    
## Tag attributes
For creating attributes, you need to call the method beginning from `set` and next with the same name as tag attribute in `CamelCase` format. 
It will be converted to lower case and splitted by dash. When content of attribute does not presented, the only attribute will be passed to the tag.

    $builder
        ->tag('!DOCTYPE')->setHtml()->endOpened()
        ->html()->setLang('en')
            ->body()
                ->div()->setClass('container')
                    ->div()->setClass('row')
                        ...
                    ->end()
                ->end()
            ->end()
        ->end();
    
    //Result
    <!DOCTYPE html><html lang="en"><body><div class="container"><div class="row"></div></div></body></html>
    
## Tag content
For adding a content to the tag, you need to call the `append` or `prepend` methods.

* The `prepend` method will add the content before all of inner tags(right after the opened tag)
* The `append` method will add the content after all of inner tags(right before the closed tag)

There also possible to append the content when creating the tag.

    $builder
        ->div()
            ->append('Appended text')
            ->prepend('Prepended text')
            ->h1('Demo')->end()
            ->p('PhpHtmlBuilder makes code easier')->end()
        ->end();
        
    //Result
    <div>Prepended text<h1>Demo</h1><p>PhpHtmlBuilder makes code easier</p>Appended text</div>

## Render

For rendering the html, you need to call the `build` method.

    $builder = new PhpHtmlBuilder();
    // ...
    $html = $builder->build();
    echo $html;
    
Or simply use the builder as string.
   
    $builder = new PhpHtmlBuilder();
    // ...
    echo $builder;
    
#License

PhpHtmlBuilder is licensed under the MIT License - see the `LICENSE` file for details