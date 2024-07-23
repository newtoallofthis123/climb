<?php
namespace ClimbUI\Render;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;

class UIInput extends HTML{
    public function __construct(string $name, string $value, bool $readonly = false)
    {
        $attributes = [
            'name' => $name,
            'value' => $value,];
        if ($readonly) {
            $attributes['readonly'] = true;
        }

        parent::__construct(
            tag: 'input',
            attributes: $attributes 
        );
    }
};