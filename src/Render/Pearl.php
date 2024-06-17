<?php

namespace ClimbUI\Render;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\Container;
use \Approach\Render\HTML;
use \Approach\Render\Node;
use \Approach\Render\Stream;
use \Approach\Render\Attribute;
use \Stringable;

/* 
 * Pearl
 *
 * A Pearl is a self expanding list item that can be used to create a visual representation of a list
 * in a Oyster. 
 * It takes in a visual representation of the list, a label for the list, and a list of children pearls
 *
 * @param string|null $visual - the visual representation of the list
 * @param string|null $label - the label of the list
 * @param Pearl|null $children - the children of the list
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
 * @return Pearl
 * */

class Pearl extends HTML
{
    public HTML $visual;
    public HTML|string|Stringable $label;
    // The array|Container is used to represent the empty state
    // cause HTML doesn't consider null values
    // This is cause we wouldn't want blank ul's that are
    // uninitialized
    public null|array|Container|HTML $children;

    public function __construct(
        null|string|HTML|Stringable $visual = null,
        null|string|HTML|Stringable $label = null,
        null|array|Container $children = null,

        public null|string|Stringable $id = null,
        null|string|array|Node|Attribute $classes = null,
        public null|array|Attribute $attributes = new Attribute,
        public $content = null,
        public array $styles = [],
        public bool $prerender = false,
        public bool $selfContained = false,
    ) {
        parent::__construct(
            tag: 'li',
            id: $id,
            classes: $classes,
            attributes: new Attribute('data-pearl', $label), 
            styles: $styles,
            prerender: $prerender,
            selfContained: $selfContained
        );

        // force the visual to be HTML
        if (!($visual instanceof HTML)) {
            // If visual was simply blank, create default visual
            if ($visual === null) {
                $visual = new HTML(tag: 'div');
                $visual[] = new HTML(tag: 'i');
                // NOTE: The Label content may be used as a fallback
                $visual[] = $this->label = new HTML(tag: 'label', content: $label);
                $visual[] = new HTML(tag: 'i', classes: ['fas', 'fa-angle-right']);
            }

            // If visual was anything else besides blank or HTML use it as content
            else {
                $visual = new HTML('div', content: $visual);
            }
        }

        $this->nodes[] = $visual;
        $this->visual = &$this->nodes[0];
        
        if ($children !== null) {
            $this->children = new HTML(tag: 'ul', classes: ['Pearl']);
            $index = count($this->nodes);
            foreach ($children as $child) {
                $this->children[$child->label] = $child;
            }
        } else {
            // if there are no children, we create an empty container
            $this->children = new \Approach\Render\Container();
        }

        $this->nodes[] = $this->children;
        $this->children = &$this->nodes[count($this->nodes) - 1];
    }

    /**
     * Add a pearl to the children list
     * 
     * @param Pearl $pearl The pearl to add
     * @return self
     */
    public function addPearl(Pearl $pearl): self
    {
        if(!($this->children instanceof \Approach\Render\HTML)){    
            
            $this->children = new HTML(tag: 'ul');
        }
        
        $this->children[$pearl->label] = $pearl;

        return $this;
    }

    /**
     * Populate child levels of this pearl with the given array
     * 
     * @param array $array An array of pearls to add
     *      Each pearl should be an associative array with the following keys:
     *          visual: string | HTML | null
     *          label: string | HTML
     *          children: array | null
     * @return self
     */
    public function populate(array $array): self
    {
        foreach ($array as $pearl) {
            $this->addPearl(new Pearl(
                // can be visual, but need not be
                visual: $pearl['visual'] instanceof HTML 
                    ? $pearl['visual'] : new Visual(title: $pearl['visual']) , 
                label: $pearl['label'],
                children: $pearl['children'] ?? null
            ));
        }
        return $this;
    }
    /**
     * Create a Pearl from an array
     * @param array<int,mixed> $array
     */
    public static function fromArray(array $array): self
    {
        return (new Pearl)->populate($array);
    }
}
