<?php


namespace App\Models\farmer;

use App\Models\farmer\FarmModel;
use App\Models\User;
use App\Models\ServicesModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;

class FarmerReportsModel extends Model
{
    use HasFactory;

    protected $table = 'farmer_reports';
    protected $fillable = [
        'farmer_id',
        'farm_id',
        'technician_id',
        'farmer_name',
        'contact_no',
        'farm_location',
        'farm_size',
        'coconut_trees',
        'coconut_variety',
        'symptoms',
        'disease',
        'image_path',
    ];

    public function farms() {
        return $this->belongsTo(FarmModel::class, 'farm_id', 'id'); 
    }

    public function technician() {
        return $this->belongsTo(User::class, 'technician_id', 'id');
    }

    public function services() {
        return $this->belongsTo(ServicesModel::class, 'farmer_id', 'farmer_id');
    }

    // Add polymorphic relationship
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'report');
    }
}



// namespace App\Models\farmer;

// use App\Models\farmer\FarmModel;
// use App\Models\User;
// use App\Models\ServicesModel;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class FarmerReportsModel extends Model
// {
//     use HasFactory;

//     protected $table = 'farmer_reports';
//     protected $fillable = [
//         'farmer_id',
//         'farm_id',
//         'technician_id',
//         'farmer_name',
//         'contact_no',
//         'farm_location',
//         'farm_size',
//         'coconut_trees',
//         'coconut_variety',
//         'symptoms',
//         'disease',
//         'image_path',
//         'status'
//     ];

//     public function farms() {
//         return $this->belongsTo(FarmModel::class, 'farm_id', 'id'); 
//     }
//     public function technician() {
//         return $this->belongsTo(User::class, 'technician_id', 'id');
//     }
//     public function services() {
//         return $this->belongsTo(ServicesModel::class, 'farmer_id', 'farmer_id');
//     }

// }
