<?php


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class ServerCest
{
    public function checkService(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->tryToSendAjaxPostRequest('/service.php', ['json' => 'json="{\"support\":{\"_response_target\":\"#content\"},\"command\":{\"REFRESH\":{\"Climb\":\"New\"}}}"']);
    }
}
