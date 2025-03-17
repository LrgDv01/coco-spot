<?php

namespace App\Models\technician;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServicesModel;
use App\Models\farmer\FarmerReportsModel;
use App\Models\farmer\FarmModel;

class TechiniciansReportsModel extends Model
{
    use HasFactory;

    protected $table = 'technician_reports';
    protected $fillable = [
        'technician_id',
        'farm_id',
        'farmer_name',
        'contact_no',
        'recipient',
        'farm_location',
        'district',
        'farm_size',
        'coconut_trees',
        'coconut_variety',
        'diagnosis_disease',
        'treatment_recommendation',
        'image_proof',
        'status',
        'no_of_infected',
        'coordinates'
    ];
    
    protected $casts = [
        'coordinates' => 'array', // Automatically cast JSON to/from array
    ];

    public function services()
    {
        return $this->belongsTo(ServicesModel::class, 'technician_id', 'technician_id');
    }

    public function farms() {
        return $this->belongsTo(FarmModel::class, 'farm_id', 'id');
    }

    // public function technician()
    // {
    //     return $this->belongsTo(TechnicianModel::class, 'technician_id', 'id');
    // }

    // public function farmerReports()
    // {
    //     return $this->belongsTo(FarmerReportsModel::class, 'farm_id', 'farm_id');
    // }

}

