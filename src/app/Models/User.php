<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $nick_name
 * @property int $status
 * @property string $sign
 * @property string $email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class User extends Model
{
	protected $table = 'user';

	protected $casts = [
		'status' => 'int'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'username',
		'password',
		'salt',
		'nick_name',
		'status',
		'sign',
		'email'
	];
}
