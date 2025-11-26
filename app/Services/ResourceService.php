<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ResourceService
{
    protected Model|null $resource;

    public function use(Model $resource): self
    {
        $this->resource = $resource;

        return $this;
    }
}
