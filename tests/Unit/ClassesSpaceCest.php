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
        $testHtml = new HTML(tag: 'div', classes: ['test', 'test2']);
        $testHtml->classes[] = 'test3';

        print($testHtml);

        $I->assertEquals('<div class="test test2 test3"></div>', $testHtml);
    }
}
