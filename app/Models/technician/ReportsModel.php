<?php

namespace App\Models\technician;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

