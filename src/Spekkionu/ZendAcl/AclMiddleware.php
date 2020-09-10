<?php

namespace Spekkionu\ZendAcl;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Config\Repository as Config;
use Laminas\Permissions\Acl\Acl;

class AclMiddleware
{

	/**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The Acl implementation.
     *
     * @var Acl
     */
    protected $acl;

    /**
     * The Config implementation.
     *
     * @var Config
     */
    protected $config;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth, Acl $acl, Config $config)
    {
        $this->auth = $auth;
        $this->acl = $acl;
        $this->config = $config;
    }

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $resource = null, $permission = null)
    {
        if ($this->auth->guest()) {
            if (!$this->acl->isAllowed('guest', $resource, $permission)) {
                return $this->notAllowed($request);
            }
        } elseif (!$this->acl->isAllowed($this->auth->user(), $resource, $permission)) {
            return $this->notAllowed($request);
        }

        return $next($request);
    }

    /**
     * Processes not allowed response
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function notAllowed(Request $request)
    {
        if ($request->ajax()) {
            return response('Unauthorized.', 401);
        } else {
            $action = $this->config->get('zendacl.action', 'redirect');
            if ($action == 'redirect') {
                $url = $this->config->get('zendacl.redirect', 'auth/login');
                return redirect($url);
            } elseif ($action == 'route') {
                $route = $this->config->get('zendacl.redirect');
                return redirect()->route($route);
            } elseif ($action == 'view') {
                $view = $this->config->get('zendacl.view', 'zendacl::unauthorized');
                return view($view);
            }
        }

    }
}
