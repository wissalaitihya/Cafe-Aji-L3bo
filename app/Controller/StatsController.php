<?php

namespace App\Controller;

/**
 * Legacy /admin/stats route — redirects to the admin dashboard
 * (kept so old sidebar links never 500).
 */
class StatsController
{
    public function index()
    {
        header('Location: ' . BASE_PATH . '/admin/dashboard');
        exit;
    }
}
