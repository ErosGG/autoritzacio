<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Link extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
     * Camps assignables massivament
     */
    protected $fillable = [
        "title",
        "url",
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
        /*
        return $this->belongsToMany(
            User::class,
            "link_user",
            "link_id",
            "user_id"
        );
        */
    }
}
