<?php

namespace Midbound\Http\Controllers;

/**
 * Class GuestController
 * @package Midbound\Http\Controllers
 */
class GuestController extends Controller
{
    /**
     * GET /
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home()
    {
        return view('guest.home');
    }

    /**
     * GET /plans
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function plans()
    {
        return view('guest.plans');
    }

    /**
     * GET /styles
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function styles()
    {
        return view('guest.styles');
    }
}
