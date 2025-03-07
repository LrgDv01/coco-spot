<?php

namespace App\Models\farmer;

use App\Models\User;
use App\Models\ServicesModel;
use App\Models\farmer\FarmerReportsModel;
use App\Models\technician\TechiniciansReportsModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmModel extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\farmer\FarmFactory::new();
    }

    protected $table = 'farms';
    protected $fillable = [
        'farmer_id',
        'name',
        'latitude',
        'longitude',
        'address',
        'municipal',
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
        return $this->hasMany(TechiniciansReportsModel::class, 'farm_id', 'id');
    }

    public function farmerReports() {
        return $this->hasMany(FarmerReportsModel::class, 'farm_id', 'id'); 
    }

}
