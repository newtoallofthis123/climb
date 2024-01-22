<?php

namespace ClimbUI;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';

use \Approach\Render\HTML;

$body->content = <<<EOL
<div class="Stage">
            <div id="main" class="Screen">
                <div class="LiveMenu Interface controls">
                    <section class="header">
                        <button class="backBtn">
                            <i class="expand fa fa-angle-left"></i>
                        </button>
                        <button
                            class="btn btn-secondary current-state ms-2"
                            id="menuButton"
                        >
                            <span id="menuButtonText">Location</span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <ul class="breadcrumbs" style="display: none"></ul>
                    </section>
                    <ul class="Toolbar">
                        <div class="signOut">
                            <button>
                                <p>
                                    <i class="bi bi-box-arrow-right"></i> Sign
                                    Out
                                </p>
                            </button>
                        </div>
                        <li>
                            <div class="visual">
                                <i class="icon bi bi-list-check"></i>
                                <label>Procedures</label>
                                <i class="expand fa fa-angle-right"> </i>
                            </div>
                            <ul>
                                <li>
                                    <div class="visual">
                                        <i class="icon bi bi-geo-alt"></i>
                                        <label>Procedure 1</label>
                                        <i class="expand fa fa-angle-right"></i>
                                    </div>
                                    <ul>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>Child Item Name 1</label>
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 2</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label
                                                    >Child Item Name 3
                                                </label>
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <div class="visual">
                                        <i class="icon bi bi-geo-alt"></i>
                                        <label> Procedure 2</label>
                                        <i class="expand fa fa-angle-right"></i>
                                    </div>
                                    <ul>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 1</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 2</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 3</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <div class="visual">
                                        <i class="icon bi bi-geo-alt"></i>
                                        <label> Procedure 3</label>
                                        <i class="expand fa fa-angle-right"></i>
                                    </div>
                                </li>
                                <li>
                                    <div class="visual">
                                        <i class="icon bi bi-geo-alt"></i>
                                        <label> Procedure 4</label>
                                        <i class="expand fa fa-angle-right"></i>
                                    </div>

                                    <ul>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 1</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 2</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label> Sub Item Name 3</label>
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                            <ul>
                                                <li>
                                                    <div class="visual">
                                                        <i
                                                            class="icon bi bi-geo-alt"
                                                        ></i>
                                                        <label>
                                                            Child Item Name
                                                            1</label
                                                        >
                                                        <i
                                                            class="expand fa fa-angle-right"
                                                        ></i>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="visual">
                                                        <i
                                                            class="icon bi bi-geo-alt"
                                                        ></i>
                                                        <label>
                                                            Child Item Name
                                                            2</label
                                                        >
                                                        <i
                                                            class="expand fa fa-angle-right"
                                                        ></i>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="visual">
                                                        <i
                                                            class="icon bi bi-geo-alt"
                                                        ></i>
                                                        <label
                                                            >Child Item Name
                                                            3</label
                                                        >
                                                        <i
                                                            class="expand fa fa-angle-right"
                                                        ></i>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="visual">
                                <i class="icon bi bi-clipboard"> </i>
                                <label>Incident Reports</label>
                                <i class="expand fa fa-angle-right"> </i>
                            </div>
                            <ul>
                                <li>
                                    <div class="visual">
                                        <i class="icon bi bi-geo-alt"></i>
                                        <label>Report 1</label>
                                        <i class="expand fa fa-angle-right"></i>
                                    </div>
                                </li>
                                <li>
                                    <div class="visual">
                                        <i class="icon bi bi-geo-alt"></i>
                                        <label>Report 2</label>
                                        <i class="expand fa fa-angle-right"></i>
                                    </div>

                                    <ul>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 1</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 2</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 3</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <div class="visual">
                                        <i class="icon bi bi-geo-alt"></i>
                                        <label> Report 3</label>
                                        <i class="expand fa fa-angle-right"></i>
                                    </div>
                                </li>
                                <li>
                                    <div class="visual">
                                        <i class="icon bi bi-geo-alt"></i>
                                        <label> Report 4</label>
                                        <i class="expand fa fa-angle-right"></i>
                                    </div>
                                    <ul>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>
                                                    Child Item Name 1</label
                                                >
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label>Child Item Name 2</label>
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="visual">
                                                <i
                                                    class="icon bi bi-geo-alt"
                                                ></i>
                                                <label> Sub Item Name 3 </label>
                                                <i
                                                    class="expand fa fa-angle-right"
                                                ></i>
                                            </div>

                                            <ul>
                                                <li>
                                                    <div class="visual">
                                                        <i
                                                            class="icon bi bi-geo-alt"
                                                        ></i>
                                                        <label>
                                                            Child Item Name
                                                            1</label
                                                        >
                                                        <i
                                                            class="expand fa fa-angle-right"
                                                        ></i>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="visual">
                                                        <i
                                                            class="icon bi bi-geo-alt"
                                                        ></i>
                                                        <label
                                                            >Child Item Name
                                                            2</label
                                                        >
                                                        <i
                                                            class="expand fa fa-angle-right"
                                                        ></i>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="visual">
                                                        <i
                                                            class="icon bi bi-geo-alt"
                                                        ></i>
                                                        <label
                                                            >Child Item Name
                                                            3</label
                                                        >
                                                        <i
                                                            class="expand fa fa-angle-right"
                                                        ></i>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <script src="/static/js/menu.js"></script>
                <div class="Main">
                    <div class="profile">
                        <div id="focus">
                            <div>
                                <img
                                    width="64"
                                    src="https://noobscience.vercel.app/favicon.ico"
                                    alt="A Cool Image"
                                />
                            </div>
                            <div>
                                <h3>John Doe</h3>
                                <p class="gray">Administrator</p>
                            </div>
                            <div>
                                <button>
                                    Click Me!
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
EOL;