<?php


namespace Tests\Unit;

use Approach\Imprint\Imprint;
use Approach\path;
use Approach\runtime;
use Approach\Scope;
use Tests\Support\UnitTester;

class ImprintCest
{
    public function _before(UnitTester $I)
    {
        $path_to_project = __DIR__ . '/..';
        $path_to_approach = __DIR__ . '/..';

        $this->scope = new Scope(
            path: [
                path::project->value => $path_to_project,
                path::installed->value => $path_to_approach,
            ],

            /*
             */
            mode: runtime::debug
        );
    }

    // tests
    public function generateImprint(UnitTester $I)
    {
        $fileDir = $this->scope->getPath(path::pattern);
        // FIXME: Fix the need for removing the ..
        // $fileDir = str_replace('/..', '', $fileDir);
        // $fileDir = str_replace('/tests/Unit', '', $fileDir);

        $imp = new Imprint(
            imprint: 'GitHub.xml',
            imprint_dir: $fileDir,
        );

        $success = $imp->Prepare();

        $imp->Mint('Issue');

        print(PHP_EOL . $fileDir . PHP_EOL);
    }
}
