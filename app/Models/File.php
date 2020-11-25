<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name',
        'name',
        'extension',
        'filable_id',
        'filable_type'
    ];

    public function filable()
    {
        return $this->morphTo();
    }
}
