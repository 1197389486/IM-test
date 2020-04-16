<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MessageList
 * 
 * @property int $id
 * @property int $user_id
 * @property int $target_id
 * @property int $type
 * @property string $name
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class MessageList extends Model
{
	protected $table = 'message_list';

	protected $casts = [
		'user_id' => 'int',
		'target_id' => 'int',
		'type' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'user_id',
		'target_id',
		'type',
		'name',
		'status'
	];
}
