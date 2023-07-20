<?php

namespace App\Domain\Exceptions;

use Exception;
use Illuminate\Http\Response;

class ExceptionCouldNotSubtractMoney extends Exception
{
    /**
     * @param $data
     * @return Response
     */
    public function render($data): Response
    {
        return response([
            "error" => "Could not subtract {$data->amount} because you can not go below account Limit",
            "code" => 400,
        ]);
    }
}
