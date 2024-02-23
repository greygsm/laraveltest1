<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class RandomUsersController extends Controller
{
    /**
     * Random User main page.
     *
     * @return View|Factory
     */
    public function index(): View|Factory
    {
        return view('randomusers_index');
    }
}
