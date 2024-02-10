<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpsecConnection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user',
        'verb',
        'connect',
        'uniqueid',
        'reqid',
        'peer',
        'ip',
        'bytes_in',
        'bytes_out',
        'seconds',
        'year',
        'month',
        'day',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (self $model) {

            $model->update([
                'year' => $model->created_at->copy()->format("Y"),
                'month' => $model->created_at->copy()->format("n"),
                'day' => $model->created_at->copy()->format("j"),
            ]);

            if ($model->verb == "down-client") {

                $upClient = static::whereUser($model->user)
                    ->whereVerb("up-client")
                    ->whereConnect($model->connect)
                    ->whereUniqueid($model->uniqueid)
                    ->whereReqid($model->reqid)
                    ->wherePeer($model->peer)
                    ->whereIp($model->ip)
                    ->orderByDesc('id')
                    ->first();

                if ($upClient) {
                    $upClient->update([
                        'seconds' => $upClient->created_at->diffInSeconds($model->created_at),
                    ]);
                }
            }

        });
    }
}
