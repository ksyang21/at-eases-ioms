<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
	use HasFactory;

	public    $timestamps = true;
	protected $fillable   = [
		'group_id',
		'seller_id',
		'status'
	];
}
