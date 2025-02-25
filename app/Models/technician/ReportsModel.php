<?php

namespace App\Models\technician;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServicesModel;
use App\Models\Farm\FarmModel;



class ReportsModel extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $fillable = [
        'technician_id',
        'farm_id',
        'farmer_name',
        'contact_no',
        'recipient',
        'farm_name',
        'farm_location',
        'district',
        'farm_size',
        'coconut_trees',
        'coconut_variety',
        'soil_type',
        'disease_type',
        'note',
    ];

    public function services()
    {
        return $this->belongsTo(ServicesModel::class, 'technician_id', 'technician_id');
    }

    public function farms() {
        return $this->belongsTo(FarmModel::class, 'farm_id', 'id');
    }
    public function technician()
{
    return $this->belongsTo(TechnicianModel::class, 'technician_id', 'id');
}

}

