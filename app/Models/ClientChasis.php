<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientChasis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'client_chasis';

    protected $fillable = [
        'client_id',
        'chasis',
        'vehiculo_id',
        'year',
    ];

    protected $hidden = [
        'client_id',
        'vehiculo_id',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['deleted_at'];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public static function updateElement($data)
    {
        $client = Client::find($data->client_id);
        if (
            $client->reference_id === 'ID362' ||
            $client->reference_id === 'ID5397' ||
            $client->reference_id === 'ID985' ||
            $client->reference_id === 'ID107' ||
            $client->reference_id === 'ID999' ||
            $client->reference_id === 'ID145' ||
            $client->reference_id === 'ID4824' ||
            $client->reference_id === 'ID9279' ||
            $client->reference_id === 'ID8600' ||
            $client->reference_id === 'ID7516' ||
            $client->reference_id === 'ID709' ||
            $client->reference_id === 'ID2932' ||
            $client->reference_id === 'ID1732'
        ) {
            /*  5397, 985, 107, 999, 145, 4824, 9279, 8600, 7516, 709, 2932, 1732 */
            return false;
        }

        $information_source = Table::find($data->information_source_id);
        if ($information_source->value === 'wap-mecanicos') {
            return false;
        }

        /* Cuando se envia chasis */
        if (!$data->chasis) {
            $cc = self::where('client_id', $data->client_id)
                ->whereNull('chasis')
                ->where('vehiculo_id', $data->vehiculo_id)
                ->first();

            if ($cc) {
                return $cc->update($data->client_chasis);
            } else {
                return self::create($data->client_chasis);
            }
        }

        if ($data->chasis) {
            $cc = self::where('client_id', $data->client_id)
                ->where('chasis', $data->chasis)
                ->first();

            if ($cc) {
                return $cc->update($data->client_chasis);
            }

            $cc = self::where('client_id', $data->client_id)
                ->whereNull('chasis')
                ->where('vehiculo_id', $data->vehiculo->id)
                ->first();

            if ($cc) {
                return $cc->update($data->client_chasis);
            } else {
                return self::create($data->client_chasis);
            }
        }
    }
}
