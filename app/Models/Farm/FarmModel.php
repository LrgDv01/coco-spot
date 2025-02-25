<?php

namespace App\Models\Farm;

use App\Models\User;
use App\Models\ServicesModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\technician\ReportsModel;


class FarmModel extends Model
{
    use HasFactory;

    protected $table = 'farms';
    protected $fillable = [
        'farmer_id',
        'name',
        'latitude',
        'longitude',
        'address',
        'district',
        'variety',
        'hectares',
        'tree_age',
        'planted_coconut',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'farmer_id', 'id');
    }

    public function services() {
        return $this->belongsTo(ServicesModel::class, 'farmer_id', 'farmer_id');
    }

    public function reports() {
        return $this->hasMany(ReportsModel::class, 'farm_id', 'id');
    }

}
