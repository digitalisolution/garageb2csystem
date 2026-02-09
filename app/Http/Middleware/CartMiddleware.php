<?php

namespace App\Http\Middleware;

// app/Http/Middleware/CartMiddleware.php


use Closure;
use Illuminate\Support\Facades\Session;

class CartMiddleware
{
    public function handle($request, Closure $next)
    {
        $cart = session('cart', []);
        $totalQuantity = array_sum(array_column($cart, 'quantity'));
        $totalPrice = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

        // Share cart data globally
        view()->share('cart', $cart);
        view()->share('cartTotalQuantity', $totalQuantity);
        view()->share('cartTotalPrice', number_format($totalPrice, 2));

        return $next($request);
    }
}
