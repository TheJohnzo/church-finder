<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Corcel\Post as Corcel;

class Post extends Corcel
{
    protected $connection = 'wordpress';
}
