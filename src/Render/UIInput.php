<?php
namespace ClimbUI\Render;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;

class UIInput extends HTML{
    public function __construct(string $name, string $value = '', bool $readonly = false, string $placeholder = '')
    {
        $attributes = [
            'name' => $name,
            'type' => 'text'
        ];
        if ($readonly) {
            $attributes['readonly'] = true;
        }

        if($placeholder != '') {
            $attributes['placeholder'] = $placeholder;
        } else{
            $attributes['value'] = $value;
        }

        parent::__construct(
            tag: 'input',
            attributes: $attributes 
        );
    }
};
