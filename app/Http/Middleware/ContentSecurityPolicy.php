<?php
namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle($request, Closure $next): Response
    {
        $response = $next($request);

        $csp = implode('; ', [
            "default-src *",
            "script-src * 'unsafe-inline' 'unsafe-eval'",
            "style-src * 'unsafe-inline'",
            "img-src * 'self' data: https:",
            "font-src * 'self' data: https:"
        ]);
        

        // Set Content-Security-Policy header (single line string)
        $response->headers->set('Content-Security-Policy', $csp);

        // Set X-Frame-Options header for additional protection
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
