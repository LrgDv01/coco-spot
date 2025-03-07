<?php

namespace App\Models;

use App\Models\User;
use App\Models\farmer\FarmModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\farmer\FarmerReportsModel;
use App\Models\technician\TechiniciansReportsModel;

class ServicesModel extends Model
{
    use HasFactory;
    
    protected $table = "services";
    protected $fillable = [
        'farmer_id',
        'technician_id',
        'farmer_name',
        'request_id',
        'status', 
    ];

    // Define relationships if needed
    public function user()
    {
        return $this->belongsTo(User::class, 'farmer_id', 'id');
    }

    public function farms()
    {
        // return $this->belongsTo(FarmModel::class, 'farmer_id', 'farmer_id');
        return $this->belongsTo(FarmModel::class, 'id');
    }

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';

    // Accessor for formatted status
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ACCEPTED:
                return 'Accepted';
            case self::STATUS_DECLINED:
                return 'Declined';
            case self::STATUS_PENDING:
                return 'Pending';
            default:
                return 'Pending'; 
        }
    }

    public function userFarms() {
        return $this->hasOne(FarmModel::class, 'farmer_id', 'farmer_id');
    }

    public function reports() {
        return $this->hasOne(TechiniciansReportsModel::class, 'technician_id', 'technician_id');
    }

    public function farmerReports() {
        return $this->hasOne(FarmerReportsModel::class, 'farmer_id', 'farmer_id'); 
    }

}
