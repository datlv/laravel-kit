<?php namespace Datlv\Kit\Testing;

use Datlv\User\User;

/**
 * Class IntegrationTestCase
 * @package Datlv\Kit\Testing
 * @author Datlv
 */
class IntegrationTestCase extends TestCase
{
    /**
     * @var \Datlv\User\User[]
     */
    protected $users = [];

    public function setUp()
    {
        parent::setUp();
        $this->withFactories(__DIR__ . "/../../../laravel-user/database/factories");
        $this->users['user'] = factory(User::class)->create();
        $this->users['admin'] = factory(User::class)->create();
        $this->users['super_admin'] = factory(User::class)->create(['username' => 'admin']);
        app('db')->table('role_user')->insert([
            [
                'user_id' => $this->users['admin']->id,
                'role_group' => 'sys',
                'role_name' => 'admin',
            ],
            [
                'user_id' => $this->users['super_admin']->id,
                'role_group' => 'sys',
                'role_name' => 'sadmin',
            ],
        ]);
    }

    /**
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return array_merge(
            parent::getPackageProviders($app),
            [
                \Datlv\User\ServiceProvider::class,
                \Datlv\Authority\ServiceProvider::class,
                \Datlv\Setting\ServiceProvider::class,
            ]
        );
    }
}
