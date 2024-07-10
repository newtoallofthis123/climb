<?php

/*
 * This is the main content that is rendered on the screen
 *
 * TODO: Split this into smaller components and use Render or Imprint Layer
 */

namespace ClimbUI;

global $body;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';

$body->content = <<<HTML
    <div class="Stage">
        <div id="main" class="Screen">
            <div class="Oyster InterfaceContent controls animate__animated animate__fadeIn">
                <section class="header controls">
                    <button class="backBtn">
                        <div>
                            <i class="expand fa fa-angle-left"></i>
                        </div>
                    </button>
                    <button
                        class="controls btn btn-warning current-state ms-2 animate__animated animate__slideInDown"
                        id="menuButton"
                    >
                        <span id="menuButtonText"><span></span></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="breadcrumbs" style="display: none"></ul>
                    <span id="newButton">
                         <button
                            class="control btn btn-primary current-state ms-2 animate__animated animate__slideInDown"
                            id="newButton"
                            data-api="/server.php"
                            data-api-method="POST"
                            data-intent='{ "REFRESH": { "Climb" : "New" } }'
                            data-context='{ "_response_target": "#content > div", "parent_id": "", "repo": "test_for_issues", "owner": "newtoallofthis123" }'
                        >
                            New
                        </button>
                    </span>
                </section>
                <ul class="Toolbar controls">
                    <li>
                        <div class="visual" style="padding: 5px 12px">
                            <button
                            class="control btn"
                            data-api="/server.php"
                            data-api-method="POST"
                            data-intent='{ "REFRESH": { "Climb" : "Menu" } }'
                            data-context='{ "_response_target": ".Toolbar > .active > ul", "climb_id": "28", "owner": "newtoallofthis123", "repo": "test_for_issues"}'
                            >
                                View
                            </button>
                            <label>Root</label>
                            <i class="expand fa fa-angle-right"> </i>
                        </div>
                        <ul></ul>
                    </li>
                </ul>
            </div>
            <div class="Viewport">
                <div id="content">
                    <div></div>
                </div>
                <div id="result"></div>
            </div>
        </div>
    </div>
    HTML;
