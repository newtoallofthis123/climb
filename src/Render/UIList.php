<?php

namespace ClimbUI\Render;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;

/**
 * UIList
 *
 * The UIList class is used to render a unordered list with a given list of items.
 *
 * @param array $arr An array of string items to display
 * @see HTML
 *
 * @return UIList
 */
class UIList extends HTML
{
    public function __construct(array $arr)
    {
        parent::__construct(
            tag: 'ul',
        );
        foreach ($arr as $key => $value) {
            if ($value instanceof HTML) {
                $this->nodes[] = $value;
            } else {
                $this->nodes[] = new HTML(
                    tag: 'li',
                    content: $value
                );
            }
        }
    }
}
