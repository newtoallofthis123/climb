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
            <div class="Oyster Interface controls animate__animated animate__fadeIn">
                <section class="header">
                    <button class="backBtn">
                        <div>
                            <i class="expand fa fa-angle-left"></i>
                        </div>
                    </button>
                    <button
                        class="btn btn-secondary current-state ms-2 animate__animated animate__slideInDown"
                        id="menuButton"
                    >
                        <span id="menuButtonText">Location</span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="breadcrumbs" style="display: none"></ul>
                </section>
                <ul class="Toolbar controls">
                    <div class="signOut">
                    </div>
                    <li>
                        <div class="visual" style="padding: 5px 12px">
                            <div
                            class="control"
                            data-api="/server.php"
                            data-api-method="POST"
                            data-intent='{ "REFRESH": { "Climb" : "Menu" } }'
                            data-context='{ "_response_target": ".Toolbar > .active > ul", "climb_id": "28", "owner": "newtoallofthis123", "repo": "test_for_issues"}'
                            >
                                <i class="icon bi bi-list-check"></i>
                            </div>
                            <label>Procedures</label>
                            <i class="expand fa fa-angle-right"> </i>
                        </div>
                        <ul></ul>
                    </li>
                </ul>
            </div>
            <div class="Viewport">
                <div id="some_content">
                    <div></div>
                </div>
                <div id="result"></div>
            </div>
        </div>
    </div>
    HTML;
