<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Movie extends Eloquent
{
    protected $collection = 'movies_collection';
}
