<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/app.php';
require dirname(__DIR__) . '/config/constants.php';
require dirname(__DIR__) . '/config/database.php';

require dirname(__DIR__) . '/app/helpers/audit.php';
require dirname(__DIR__) . '/app/helpers/session.php';
require dirname(__DIR__) . '/app/helpers/auth.php';
require dirname(__DIR__) . '/app/helpers/csrf.php';
require dirname(__DIR__) . '/app/helpers/response.php';
require dirname(__DIR__) . '/app/helpers/security.php';
require dirname(__DIR__) . '/app/helpers/validation.php';
require dirname(__DIR__) . '/app/helpers/flash.php';
require dirname(__DIR__) . '/app/helpers/input.php';
require dirname(__DIR__) . '/app/helpers/url.php';
