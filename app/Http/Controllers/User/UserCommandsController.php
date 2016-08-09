<?php

namespace App\Http\Controllers;

/**
 * Class SiteController
 * @package App\Http\Controllers
 */
class UserCommandsController extends Controller
{
    /**
     * Downloads an invoice from stripe
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadInvoice($id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        return \Auth::user()->downloadInvoice($id, [
            'vendor' => 'CodePier',
            'product' => 'Server Management',
        ]);
    }
}
