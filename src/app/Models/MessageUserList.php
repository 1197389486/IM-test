<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MessageUserList
 * 
 * @property int $id
 * @property int $user_id
 * @property int $message_list_id
 * @property string $name
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class MessageUserList extends Model
{
	protected $table = 'message_user_list';

	protected $casts = [
		'user_id' => 'int',
		'message_list_id' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'user_id',
		'message_list_id',
		'name',
		'status'
	];
}
