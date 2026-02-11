<?php

namespace Teguh02\Rijanphp\Core\Controller;

use Teguh02\Rijanphp\Core\Http\Request;
use Teguh02\Rijanphp\Core\Http\Response;

abstract class Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Validate the request data.
     */
    protected function validate(array $rules)
    {
        // Placeholder for validation logic.
        // In a real framework, this would call a Validator service.
        return true;
    }

    /**
     * Shortcut to create a JSON response.
     */
    protected function json($data, int $status = 200)
    {
        return (new Response())->json($data, $status);
    }
}
