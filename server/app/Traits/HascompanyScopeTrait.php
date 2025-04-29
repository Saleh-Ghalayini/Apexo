<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

trait HascompanyScopeTrait
{
    /**
     * “Booting a trait” in Laravel refers to the process of 
     * initializing trait behavior in an Eloquent model. 
     * When a model boots, Laravel automatically calls methods named 
     * boot{TraitName} for each trait used by the model.
     */
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
