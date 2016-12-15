<?php

require __DIR__.'/../bootstrap/heresy.php';

require __DIR__.'/../bootstrap/module-route.php';

require Config('modules')[Config('app.active-module')]['dir'].'/bootstrap.php';
