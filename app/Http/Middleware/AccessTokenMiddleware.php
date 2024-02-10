<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Access-Token', $request->access_token);

        if (!$this->checToken($token)) {
            abort(403);
        }

        return $next($request);
    }

    /**
     * Check valid access token
     * 
     * @param string|null $token
     * @return bool
     */
    private function checToken($token = null)
    {
        return in_array($token, $this->getTokens());
    }

    /**
     * Access tokens
     * 
     * @return array
     */
    private function getTokens()
    {
        $key = file_get_contents(storage_path('access_tokens.key'));

        return decrypt(str_replace("\n", "", $key));
    }
}
