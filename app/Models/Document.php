<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $fillable = [
        'name',
        'description',
        'status',
        'type',
        'filename',
        'uploaded_by'
    ];

    public function uploader(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'uploaded_by');
    }
}
