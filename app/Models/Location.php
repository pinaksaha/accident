<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * @var string
     */
    protected $table ='locations';

    /**
     * @var string[]
     */
    protected $fillable = ['name','code','long','lat'];
}
