<?php

namespace App\Models;

use App\system\dao\Connection;

class HomeModel extends Connection
{

    public function __construct($db = 'default')
    {
        parent::__construct($db);
    }
}