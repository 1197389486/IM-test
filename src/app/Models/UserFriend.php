<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserFriend
 * 
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property int $f_id
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class UserFriend extends Model
{
	protected $table = 'user_friend';

	protected $casts = [
		'user_id' => 'int',
		'type' => 'int',
		'f_id' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'user_id',
		'type',
		'f_id',
		'status'
	];
}
