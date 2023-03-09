<?php

use Phinx\Seed\AbstractSeed;

class AdminUserInit extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run(): void
    {
        $pathLocal = getcwd()."/config/autoload/local.php";
        $localConf = array();
        if(file_exists($pathLocal)){
            $localConf = require $pathLocal;
        }
        $salt = "";
        if(!empty($localConf['monarc']['salt'])){
            $salt = $localConf['monarc']['salt'];
        }

        $email = 'admin@admin.localhost';
        $defaultPassword = 'admin';

        $data = array(
            'status' => 1,
            'firstname' => 'Admin',
            'lastname' => 'Admin',
            'language' => 1,
            'email' => $email,
            'password' => password_hash($salt.$defaultPassword,PASSWORD_BCRYPT),
            'creator' => 'System',
            'created_at' => date('Y-m-d H:i:s'),
        );

        $posts = $this->table('users');
        $posts->insert($data)
              ->save();

        $pathBo = __DIR__."/../../config/module.config.php";
        if(file_exists($pathBo)){
            $confBo = include $pathBo;
            if(!empty($confBo['roles'])){
                $rows = $this->fetchRow('SELECT id FROM users where email LIKE \''.$email.'\' LIMIT 1');
                if(!empty($rows)){
                    $posts = $this->table('users_roles');
                    foreach($confBo['roles'] as $k => $v){
                        $data = array(
                            'user_id' => $rows['id'],
                            'role' => $k,
                            'creator' => 'System',
                            'created_at' => date('Y-m-d H:i:s'),
                        );
                        $posts
                            ->insert($data)
                            ->save();
                    }
                }
            }
        }
    }
}
