<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comment;
use App\Models\Reaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $post->comments()->delete();
            $post->reactions()->delete();
        });
    }
    
    protected $fillable = ['user_id', 'caption', 'image_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function likesCount()
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function dislikesCount()
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }
}
