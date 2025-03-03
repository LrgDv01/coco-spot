<?php

namespace App\Models\farmer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmerReportsModel extends Model
{
    use HasFactory;

    protected $table = 'farmer_reports';
    protected $fillable = [
        'farmer_id',
        // 'farm_id',
        'farmer_name',
        'contact_no',
        'farm_location',
        'farm_size',
        'coconut_trees',
        'coconut_variety',
        'symptoms',
        'disease',
        'image_path',
        'status'
    ];

}
