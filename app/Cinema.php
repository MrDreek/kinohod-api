<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Cinema extends Eloquent
{
    protected $collection = 'cinemas_collection';
}
