<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Attribute\Route;
use App\Services\InvoiceService;
use App\View;

class HomeController
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    #[Route('/')]
    public function index(): View
    {
        $this->invoiceService->process([], 25);

        return View::make('index');
    }
}
