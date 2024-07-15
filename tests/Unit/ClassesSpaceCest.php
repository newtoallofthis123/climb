<?php


namespace Tests\Unit;

use Tests\Support\UnitTester;
use \Approach\Render\HTML;

class ClassesSpaceCest
{
    public function _before(UnitTester $I): void
    {
    }

    // tests
    public function tryToTest(UnitTester $I): void
    {
        $testHtml = new HTML(tag: 'div', classes: ['test ', 'test2']);
        $I->assertEquals('<div class="test test2"></div>', $testHtml->render());
    }
}
