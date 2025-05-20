<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

trait HasCompanyScopeTrait
{
    protected static function bootHasCompanyScopeTrait()
    {
        static::addGlobalScope(
            'company',
            function (Builder $builder) {
                $user = Auth::user();

                if ($user && $builder->getModel()->getTable() !== 'users')
                    $builder->where($builder->getModel()->getTable() . '.company_id', $user->company_id);
            }
        );
    }
}
