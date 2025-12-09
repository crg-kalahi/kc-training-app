<?php
namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle($request, Closure $next): Response
    {
        $response = $next($request);

        // Check environment variable to determine if HTTPS should be forced
        // If FORCE_HTTPS exists in .env and is true, force HTTPS (production)
        // If FORCE_HTTPS doesn't exist or is false, allow HTTP (local development)
        $forceHttpsEnv = env('FORCE_HTTPS');
        $isProduction = config('app.env') === 'production';
        
        // If FORCE_HTTPS is explicitly set in .env, use it (convert string to boolean)
        // Otherwise, default to production check
        if ($forceHttpsEnv !== null) {
            $forceHttps = filter_var($forceHttpsEnv, FILTER_VALIDATE_BOOLEAN);
        } else {
            // If not set in .env, default based on environment
            // Production: force HTTPS, Local: allow HTTP
            $forceHttps = $isProduction;
        }
        
        if ($forceHttps) {
            // Stricter CSP for production (when FORCE_HTTPS is true or in production)
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
            // More permissive CSP for development (when FORCE_HTTPS is false or not set in local)
            $csp = implode('; ', [
                "default-src *",
                "script-src * 'unsafe-inline' 'unsafe-eval'",
                "style-src * 'unsafe-inline'",
                "img-src * 'self' data: https:",
                "font-src * 'self' data: https:"
                // Removed 'upgrade-insecure-requests' for local development
            ]);
        }
        
        // Set Content-Security-Policy header
        $response->headers->set('Content-Security-Policy', $csp);

        // Set additional security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Only set HSTS when HTTPS is forced (production or FORCE_HTTPS=true)
        // This avoids SSL errors in local development
        if ($forceHttps) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}