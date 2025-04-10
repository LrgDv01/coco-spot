<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapModel extends Model
{
    use HasFactory;
    public $table = 'map_locations';
    protected $fillable = [
        'name',
        'coordinates',
        'color',
        'lot_area',
        'trees',
        'meters',
    ];
}
