<?php

use Phinx\Seed\AbstractSeed;

class AdminUserInit extends AbstractSeed
{
    public function run(): void
    {
        $pathLocal = getcwd() . '/config/autoload/local.php';
        $localConf = [];
        if (file_exists($pathLocal)) {
            $localConf = require $pathLocal;
        }
        $email = 'admin@admin.localhost';
        $defaultPassword = 'admin';

        $posts = $this->table('users');
        $posts->insert([
            'status' => 1,
            'firstname' => 'Admin',
            'lastname' => 'Admin',
            'language' => 1,
            'email' => $email,
            'password' => password_hash($defaultPassword, PASSWORD_BCRYPT),
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s'),
        ])->save();

        $pathBo = __DIR__ . "/../../config/module.config.php";
        if (file_exists($pathBo)) {
            $confBo = include $pathBo;
            if (!empty($confBo['roles'])) {
                $rows = $this->fetchRow('SELECT id FROM users where email LIKE \'' . $email . '\' LIMIT 1');
                if (!empty($rows)) {
                    $posts = $this->table('users_roles');
                    foreach ($confBo['roles'] as $k => $v) {
                        $posts->insert([
                            'user_id' => $rows['id'],
                            'role' => $k,
                            'creator' => 'System',
                            'created_at' => date('Y-m-d H:i:s'),
                        ])->save();
                    }
                }
            }
        }
    }
}
