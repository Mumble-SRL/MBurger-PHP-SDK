<?php

namespace Mumble\MBurger\Tests;

use Mumble\MBurger\MBurger;
use Mumble\MBurger\Exceptions\MBurgerUnauthenticatedException;

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
