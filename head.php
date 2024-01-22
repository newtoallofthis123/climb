<?php

namespace ClimbUI;

// This the head file Render
// I can now include this wherever I need head stuff

require_once __DIR__ . '/support/lib/vendor/autoload.php';

use \Approach\Render\HTML;

// Can also be done like this
//$head->content .= file_get_contents(__DIR__ . '/support/static/head.htm'); //optional

$head->content .= <<<EOL
<meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta
            name="viewport"
            content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0"
        />
        <meta name="author" content="Ishan Joshi" />
        <link
            rel="stylesheet"
            href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"
        />
        <link rel="stylesheet" type="text/css" href="/static/css/layout.css" />
        <link rel="stylesheet" type="text/css" href="/static/css/style.css" />
        <link rel="stylesheet" type="text/css" href="/static/css/reset.css" />
        <link rel="stylesheet" type="text/css" href="/static/css/menu.css" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"
        ></script>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
        />
        <!-- Just for the aesthetics -->
        <link
            rel="shortcut icon"
            href="https://noobscience.vercel.app/favicon.ico"
            type="image/x-icon"
        />
        <title>Home Page</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
        <script
            type="text/javascript"
            src="/static/js/approach.interface.js"
        ></script>
EOL;