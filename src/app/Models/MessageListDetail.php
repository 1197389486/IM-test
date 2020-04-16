<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MessageListDetail
 * 
 * @property int $id
 * @property int $message_from
 * @property int $message_to
 * @property int $message_list_id
 * @property int $type
 * @property int $status
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class MessageListDetail extends Model
{
	protected $table = 'message_list_detail';

	protected $casts = [
		'message_from' => 'int',
		'message_to' => 'int',
		'message_list_id' => 'int',
		'type' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'message_from',
		'message_to',
		'message_list_id',
		'type',
		'status',
		'content'
	];
}
