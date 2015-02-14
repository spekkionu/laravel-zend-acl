<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Unauthorized Action
    |--------------------------------------------------------------------------
    |
    | The action performed when a route is requested that the user does not have permission to
    | access.
    |
    | Can be redirect, route or view.
    | If redirect is selected the user will be redirected to a selected url.
    | If route is selected the user will be redirected to a selected route.
    | If view is selected the selected view will be rendered.
    |
    */

    'action' => 'redirect',

    /*
    |--------------------------------------------------------------------------
    | Unauthorized Redirect Url
    |--------------------------------------------------------------------------
    |
    | The url the user will be redirected to if the action is redirect or
    | the named route if the action is route.
    |
    */
    'redirect' => 'auth/login',

    /*
    |--------------------------------------------------------------------------
    | Unauthorized View
    |--------------------------------------------------------------------------
    |
    | The view that will be rendered if the action is view.
    |
    */
    'view' => 'zendacl::unauthorized'

);
