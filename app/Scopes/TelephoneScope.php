<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TelephoneScope implements Scope
{
    protected $telephone;

    public function __construct($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!is_null($this->telephone)) {
            $builder->where('telephone', $this->telephone);
        }
    }
}
