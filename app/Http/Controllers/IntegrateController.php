<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\AmoCrmService;
use Symfony\Component\HttpFoundation\Response;

class IntegrateController extends Controller
{

    private AmoCrmService $amoCrmService;

    public function __construct(AmoCrmService $amoCrmService)
    {
        $this->amoCrmService = $amoCrmService;
    }

    public function index()
    {
        $leads = $this->amoCrmService
            ->connect()
            ->getLeads();

        $leads = $leads->toArray();

        foreach ($leads as $lead) {
            Lead::query()
                ->create($lead);
        }

        return response('Created successfully')
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
