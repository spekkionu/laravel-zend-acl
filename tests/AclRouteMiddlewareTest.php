<?php namespace Spekkionu\ZendAcl;

use PHPUnit\Framework\TestCase;
use Spekkionu\ZendAcl\AclMiddleware;
use \Mockery as m;
use Closure;

function view($view) {
    return AclRouteMiddlewareTest::$functions->view($view);
}

function redirect($url = null) {
    return AclRouteMiddlewareTest::$functions->redirect($url);
}

function response($body = null, $status = null) {
    return AclRouteMiddlewareTest::$functions->response($body, $status);
}

class AclRouteMiddlewareTest extends TestCase
{

    public static $functions;

    public function setUp(): void
    {
        self::$functions = m::mock();
    }

    public function tearDown(): void {
        m::close();
    }

    public function testFilter()
    {
        $resource = 'article';
        $permission = 'view';

        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $auth->shouldReceive('guest')->once()->andReturn(false);
        $auth->shouldReceive('user')->once()->andReturn('member');

        $config = m::mock('Illuminate\Contracts\Config\Repository');


        $acl = m::mock('Laminas\Permissions\Acl\Acl');
        $acl->shouldReceive('isAllowed')->once()->with('member', $resource, $permission)->andReturn(true);

        $closure = function($request){};

        $request = m::mock('Illuminate\Http\Request');

        $filter = new AclMiddleware($auth, $acl, $config);
        $allowed = $filter->handle($request, $closure, $resource, $permission);
    }

    public function testFilterAuthFail()
    {
        $resource = 'article';
        $permission = 'view';

        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $auth->shouldReceive('guest')->once()->andReturn(false);
        $auth->shouldReceive('user')->once()->andReturn('member');

        $config = m::mock('Illuminate\Contracts\Config\Repository');
        $config->shouldReceive('get')->with('zendacl.action', 'redirect')->andReturn('view');
        $config->shouldReceive('get')->with('zendacl.view', 'zendacl::unauthorized')->andReturn('zendacl::unauthorized');


        $acl = m::mock('Laminas\Permissions\Acl\Acl');
        $acl->shouldReceive('isAllowed')->once()->with('member', $resource, $permission)->andReturn(false);

        $closure = function($request){};

        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('ajax')->once()->andReturn(false);

        self::$functions->shouldReceive('view')->once()->with('zendacl::unauthorized');

        $filter = new AclMiddleware($auth, $acl, $config);
        $allowed = $filter->handle($request, $closure, $resource, $permission);
    }

    public function testFilterForGuest()
    {
        $resource = 'article';
        $permission = 'view';

        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $auth->shouldReceive('guest')->once()->andReturn(true);

        $config = m::mock('Illuminate\Contracts\Config\Repository');

        $acl = m::mock('Laminas\Permissions\Acl\Acl');
        $acl->shouldReceive('isAllowed')->once()->andReturn(true)->with('guest', $resource, $permission);

        $closure = function($request){};

        $request = m::mock('Illuminate\Http\Request');

        $filter = new AclMiddleware($auth, $acl, $config);
        $allowed = $filter->handle($request, $closure, $resource, $permission);
    }

    public function testFilterFailedView()
    {
        $resource = 'article';
        $permission = 'view';

        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $auth->shouldReceive('guest')->once()->andReturn(true);

        $config = m::mock('Illuminate\Contracts\Config\Repository');
        $config->shouldReceive('get')->with('zendacl.action', 'redirect')->andReturn('view');
        $config->shouldReceive('get')->with('zendacl.view', 'zendacl::unauthorized')->andReturn('zendacl::unauthorized');

        $acl = m::mock('Laminas\Permissions\Acl\Acl');
        $acl->shouldReceive('isAllowed')->once()->andReturn(false)->with('guest', $resource, $permission);

        $closure = function($request){};

        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('ajax')->once()->andReturn(false);

        self::$functions->shouldReceive('view')->once()->with('zendacl::unauthorized');

        $filter = new AclMiddleware($auth, $acl, $config);
        $allowed = $filter->handle($request, $closure, $resource, $permission);
    }

    public function testFilterFailedRedirect()
    {
        $resource = 'article';
        $permission = 'view';

        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $auth->shouldReceive('guest')->once()->andReturn(true);

        $config = m::mock('Illuminate\Contracts\Config\Repository');
        $config->shouldReceive('get')->with('zendacl.action', 'redirect')->andReturn('redirect');
        $config->shouldReceive('get')->with('zendacl.redirect', 'auth/login')->andReturn('auth/login');

        $acl = m::mock('Laminas\Permissions\Acl\Acl');
        $acl->shouldReceive('isAllowed')->once()->andReturn(false)->with('guest', $resource, $permission);

        $closure = function($request){};

        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('ajax')->once()->andReturn(false);

        self::$functions->shouldReceive('redirect')->once()->with('auth/login')->once();

        $filter = new AclMiddleware($auth, $acl, $config);
        $allowed = $filter->handle($request, $closure, $resource, $permission);
    }

    public function testFilterFailedRoute()
    {
        $resource = 'article';
        $permission = 'view';

        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $auth->shouldReceive('guest')->once()->andReturn(true);

        $config = m::mock('Illuminate\Contracts\Config\Repository');
        $config->shouldReceive('get')->with('zendacl.action', 'redirect')->andReturn('route');
        $config->shouldReceive('get')->with('zendacl.redirect')->andReturn('login');

        $acl = m::mock('Laminas\Permissions\Acl\Acl');
        $acl->shouldReceive('isAllowed')->once()->andReturn(false)->with('guest', $resource, $permission);

        $closure = function($request){};

        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('ajax')->once()->andReturn(false);

        $redirect = m::mock('redirect');
        $redirect->shouldReceive('route')->once()->andReturn('login');

        self::$functions->shouldReceive('redirect')->once()->andReturn($redirect);

        $filter = new AclMiddleware($auth, $acl, $config);
        $allowed = $filter->handle($request, $closure, $resource, $permission);
    }

    public function testFilterFailedAjax()
    {
        $resource = 'article';
        $permission = 'view';

        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $auth->shouldReceive('guest')->once()->andReturn(true);

        $config = m::mock('Illuminate\Contracts\Config\Repository');

        $acl = m::mock('Laminas\Permissions\Acl\Acl');
        $acl->shouldReceive('isAllowed')->once()->andReturn(false)->with('guest', $resource, $permission);

        $closure = function($request){};

        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('ajax')->once()->andReturn(true);

        self::$functions->shouldReceive('response')->once()->with('Unauthorized.', 401);

        $filter = new AclMiddleware($auth, $acl, $config);
        $allowed = $filter->handle($request, $closure, $resource, $permission);
    }
}
