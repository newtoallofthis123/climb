<?php

namespace ClimbUI\Render;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;

class Quantity extends HTML
{
  public $amounts = [];
  public $units = [];

  function __construct( 
    public $amount = 0, public $unit = '')
    { 

    // Allows us to set tag, class, etc 
    parent::__construct(
      tag: 'div'
    );

    $labels = new HTML( tag: 'div' ); 
    $labels[] = new HTML( tag: 'span', classes: ['amount'], content: 'Amount' );
    $labels[] = new HTML( tag: 'span', content: ' | ' );
    $labels[] = new HTML( tag: 'span', classes: ['unit'], content: 'Unit' );
    
    $this->nodes[] = $labels;
    
    $amount_input = new HTML( tag: 'input' );
    $amount_input->attributes['name'] = 'amount';
    $amount_input->attributes['type'] = 'text';

    // Make $this->amount changes the value inside the input live
    $amount_input->attributes['value'] = $amount;

    $unit_input = new HTML( tag: 'input' );
    $unit_input->attributes['name'] = 'unit';
    $unit_input->attributes['type'] = 'text';

    // Make $this->unit changes the value inside the input live
    $unit_input->attributes['value'] = $unit;

    $this->nodes[] = $amount_input;
    $this->nodes[] = $unit_input;

  }
}
