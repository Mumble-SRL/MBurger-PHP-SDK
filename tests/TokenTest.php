<?php

namespace Mumble\MBurger\Tests;

use Illuminate\Support\Arr;
use Mumble\MBurger\Exceptions\MBurgerUnauthenticatedException;
use Mumble\MBurger\MBurger;

class TokenTest extends TestCase
{
    public function test_wrong_missing_token()
    {
        $this->app['config']->set('mburger.api_key', null);

        try {
            (new MBurger())->getProject();
        } catch (MBurgerUnauthenticatedException $e) {
            $this->assertEquals('The project token is not present.', $e->getMessage());
        }
    }

    public function test_wrong_project_token()
    {
        $this->app['config']->set('mburger.api_key', 'WRONG');

        try {
            (new MBurger())->getProject();
        } catch (MBurgerUnauthenticatedException $e) {
            $this->assertEquals('The project token is not valid.', $e->getMessage());
        }
    }
}
