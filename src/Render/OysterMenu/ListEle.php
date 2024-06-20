<?php
namespace ClimbUI\Render\OysterMenu;

require_once __DIR__ . '/../../../support/lib/vendor/autoload.php';

use Approach\Render\Attribute;
use Approach\Render\HTML;
use Approach\Render\Node;
use Stringable;

/* 
 * ListEle
 *
 * The ListEle declares a ListElement that represents the list element of a pearl.
 * This is essentially a list item that can be used to create a visual representation of the list
 *
 * @param string|null $visual - the visual representation of the list
 * @param string|null $label - the label of the list
 * @param bool|null $isControl - whether or not the list is a control
 *
 * @see HTML
 *
 * @param string|null $id - the id of the list
 * @param string|array|Node|Attribute|null $classes - the classes of the list
 * @param array|Attribute|null $attributes - the attributes of the list
 * @param string|Stringable|Stream|null $content - the content of the list
 * @param array $styles - the styles of the list
 * @param bool $prerender - whether or not to prerender the list
 * @param bool $selfContained - whether or not the list is self contained
 *
 * @return ListEle
 * */
class ListEle extends HTML{
    public function __construct(
        // Passing in Visual creates a sort of hierarchy that can be used to create a visual representation of the list
        public null|string|Stringable $visual = null,
        public null|string|Stringable $label = null,
    
        public null|bool $isControl = false,

        public null|string|Stringable $id = null,
        null|string|array|Node|Attribute $classes = null,
        public null|array|Attribute $attributes = new Attribute,
        public $content = null,
        public array $styles = [],
        public bool $prerender = false,
        public bool $selfContained = false,
    ){
        $ul = new HTML(tag: 'ul', classes: ['controls']);

        // if it is a control, add the control class to the classes array
        if($this->isControl){
            if($classes == null){
                $classes = [];
            }
            $classes = array_merge($classes, ['control']);
        }

        parent::__construct(
            tag: 'li',
            id: $id,
            classes: $classes,
            attributes:  new Attribute('data-label', $label),
            styles: $styles,
            content: ($visual ? $visual : null) . $ul . $content,
            prerender: $prerender,
            selfContained: $selfContained
        );
    }
} 
