<?php

namespace App;

use Illuminate\Support\Facades\Http;

trait ExecuteExternalServiceTrait
{
    public function request($method, $url, $headers = [], $data = [])
    {
        return HTTP::withHeaders($headers)->$method($url, $data);
    }
}
