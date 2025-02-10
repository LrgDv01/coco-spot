<?php

namespace App\Models\Farm;

use App\Models\User;
use App\Models\ServicesModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmModel extends Model
{
    use HasFactory;

    public $table = 'farms';
    protected $fillable = [
        'farmer_id',
        'name',
        'location',
        'district',
        'variety',
        'hectares',
        'tree_age',
        'planted_coconut',
        'condition',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'farmer_id', 'id');
    }

    public function  services() {
        return $this->belongsTo(ServicesModel::class, 'farmer_id', 'farmer_id');
    }

}
