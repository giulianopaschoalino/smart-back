<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();

        if (checkUserId($user->client_id)) {
            $builder->join(
                "dados_cadastrais",
                "dados_cadastrais.cod_smart_unidade",
                "=",
                $model->qualifyColumn("cod_smart_unidade"),
            )->where('dados_cadastrais.cod_smart_cliente', '=', $user->client_id);


//            $sql = DB::table('dados_cadastrais')
//                ->select([
//                    $model->qualifyColumn("cod_smart_unidade")
//                ])
//                ->where($model->qualifyColumn("cod_smart_unidade"), '=', $user->client_id);
//
//            $builder->whereRaw($sql, 'in', $user->client_id);
        }
    }

}
