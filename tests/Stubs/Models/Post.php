<?php

declare(strict_types=1);

namespace LaravelQueryKit\Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';

    public $timestamps = false;

    protected $guarded = [];
}
