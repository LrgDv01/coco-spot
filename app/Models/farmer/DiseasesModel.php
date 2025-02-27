<?php

namespace App\Models\farmer;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseasesModel extends Model
{
    use HasFactory;

    public $table = 'diseases';
    protected $fillable = [
        'farm_id',
        'yellowing',
        'bud_rot',
        'leaf_spot',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
