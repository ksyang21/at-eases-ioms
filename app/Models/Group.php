<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
	public $timestamps = true;
	protected $fillable = [
		'name',
		'parent_id',
		'total_pv',
		'status'
	];

    public function members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_id')->with('parent');
    }

    public function subgroups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Group::class, 'parent_id')->with('subgroups');
    }

    public function addPv(float $point): void
    {
        $this->total_pv += $point;
        $this->update();

        if($this->parent) {
            $this->parent->addPv($point);
        }
    }

    public function deductPv(float $point): void {
        $this->total_pv -= $point;
        $this->update();

        if($this->parent) {
            $this->parent->deductPv($point);
        }
    }
}
