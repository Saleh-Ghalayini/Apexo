<?php

namespace App\Services;

class AIToolsService
{
    protected $dataAccessService;

    public function __construct($dataAccessService)
    {
        $this->dataAccessService = $dataAccessService;
    }
}
