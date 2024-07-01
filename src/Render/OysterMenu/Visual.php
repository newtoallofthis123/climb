<?php
namespace ClimbUI\Render\OysterMenu;

require_once __DIR__ . '/../../../support/lib/vendor/autoload.php';

use Approach\Render\Attribute;
use Approach\Render\HTML;
use Approach\Render\Node;
use Stringable;

/*
 * Visual
 *
 * The Visual class is used to create a visual representation of a list in a pearl.
 *
 * @param string|null $title - the title of the visual
 * @param bool|null $isTodo - whether or not the visual is a todo
 * @param string|null $id - the id of the visual
 *
 * @see HTML
 *
 * @param string|array|Node|Attribute|null $classes - the classes of the visual
 * @param array|Attribute|null $attributes - the attributes of the visual
 * @param string|Stringable|Stream|null $content - the content of the visual
 * @param array $styles - the styles of the visual
 * @param bool $prerender - whether or not to prerender the visual
 * @param bool $selfContained - whether or not the visual is self contained
 *
 * @return Visual
 */
class Visual extends HTML
{
    public function __construct(
        public null|string|Stringable $title = null,
        public null|bool $isTodo = false,
        public null|string|Stringable $id = null,
        null|string|array|Node|Attribute $classes = null,
        public null|array|Attribute $attributes = new Attribute,
        public $content = null,
        public array $styles = [],
        public bool $prerender = false,
        public bool $selfContained = false,
    ) {
        // add .visual class to the classes array
        // to make sure classes are not null
        if ($classes === null) {
            $classes = [];
        }
        $classes = array_merge($classes, [' visual']);

        $icon = new HTML('i', classes: ['icon ', 'bi ', 'bi-arrow-right']);
        $label = new HTML('label', content: ' ' . $this->title);
        $expand = new HTML('i', classes: ['expand ', 'fa ', 'fa-angle-right']);

        parent::__construct(
            tag: 'div',
            id: $id,
            classes: $classes,
            attributes: $attributes,
            // TODO: Concatenation to be converted to nodes and children
            content: $icon . ' ' . $label . $expand . $content,
            styles: $styles,
            prerender: $prerender,
            selfContained: $selfContained
        );
    }
}
