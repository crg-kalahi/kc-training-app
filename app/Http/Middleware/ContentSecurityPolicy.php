<?php
namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle($request, Closure $next): Response
    {
        $response = $next($request);

        // Determine environment-specific CSP
        $isProduction = config('app.env') === 'production';
        
        if ($isProduction) {
            // Stricter CSP for production
            $csp = implode('; ', [
                "default-src 'self' https:",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: cdnjs.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https:",
                "img-src 'self' data: https:",
                "font-src 'self' data: https:",
                "connect-src 'self' https:",
                "upgrade-insecure-requests",
                "block-all-mixed-content"
            ]);
        } else {
            // More permissive CSP for development
            $csp = implode('; ', [
                "default-src *",
                "script-src * 'unsafe-inline' 'unsafe-eval'",
                "style-src * 'unsafe-inline'",
                "img-src * 'self' data: https:",
                "font-src * 'self' data: https:",
                "upgrade-insecure-requests"
            ]);
        }
        
        // Set Content-Security-Policy header
        $response->headers->set('Content-Security-Policy', $csp);

        // Set additional security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}