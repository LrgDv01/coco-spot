<?php

namespace App\Models\farmer;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoconutVarietyModel extends Model
{
    use HasFactory;

    public $table = 'coconut_variety';
    protected $fillable = [
        'farm_id',
        'laguna_tall',
        'dwarf_coconut',
        'hybrid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
