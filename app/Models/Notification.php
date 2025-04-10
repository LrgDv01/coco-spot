<?php


namespace App\Models;

use App\Models\User;
use App\Models\farmer\FarmerReportsModel;
use App\Models\technician\TechiniciansReportsModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    
    protected $table = 'notifications';
    protected $fillable = [
        'report_id',
        'report_type', // This will store either 'App\Models\farmer\FarmerReportsModel' or 'App\Models\technician\TechiniciansReportsModel'
        'user_id',
        'farm_id',
        'disease',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Replace the separate report relationships with a polymorphic one
    public function report() {
        return $this->morphTo();
    }

    // Remove these as they're replaced by the polymorphic relationship
    // public function farmerReport() {
    //     return $this->belongsTo(FarmerReportsModel::class, 'farmer_report_id', 'id');
    // }
    
    // public function technicianReport() {
    //     return $this->belongsTo(TechiniciansReportsModel::class, 'technician_report_id', 'id');
    // }
}

// namespace App\Models;

// use App\Models\User;
// use App\Models\farmer\FarmerReportsModel;
// use App\Models\technician\TechiniciansReportsModel;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Notification extends Model
// {
//     use HasFactory;
//     protected $table = 'notifications';
//     protected $fillable = [
//         'report_id',
//         'report_type',
//         'user_id',
//         'farm_id',
//         'disease',
//         'status',
//     ];

//     public function user() {
//         return $this->belongsTo(User::class, 'user_id', 'id');
//     }
//     public function farmerReport() {
//         return $this->belongsTo(FarmerReportsModel::class, 'farmer_report_id', 'id');
//     }
    
//     public function technicianReport() {
//         return $this->belongsTo(TechiniciansReportsModel::class, 'technician_report_id', 'id');
//     }
// }
