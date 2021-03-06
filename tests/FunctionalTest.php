<?php

use Dimsav\Backup\Project;

class BackupTest extends TestBase {

    /**
     * @test
     */
    public function it_stores_the_backup_of_project_files_locally()
    {
        $config = $this->getBaseConfig();
        $config['projects']['my_project_1']['directories'] = array(
            "Dimsav/Backup/Element" => array('excludes' => array('Exceptions')
            ));

        $this->runApp($config);
        $fileRegex = '/\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_files_dimsav_backup_element\.zip/';
        $backupDir = $this->backupsDir . '/my_project_1/';
        $this->assertTrue($this->dirContainsRegexFile($backupDir, $fileRegex));
    }

    /**
     * @test
     */
    public function it_stores_the_db_backup_locally()
    {
        $config = $this->getBaseConfig();
        $config['projects']['my_project_1']['mysql'] = array(
            "test_db" => array(
                "host" => "localhost",
                "port" => "3306",
                "username" => "root",
                "password" => "secret",
            )
        );
        exec('cd ' . __DIR__ . " && mysql -u root -psecret test_db < test_db.sql");

        $this->runApp($config);
        $fileRegex = '/\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_test_db\.sql/';
        $backupDir = $this->backupsDir . '/my_project_1/';
        $this->assertTrue($this->dirContainsRegexFile($backupDir, $fileRegex));
    }

    /**
     * @test
     */
    public function it_parses_database_defaults()
    {
        $config = $this->getBaseConfig();
        $config['project_defaults']['mysql'] = array(
            "host" => "localhost",
            "port" => "3306",
        );
        $config['projects']['my_project_1']['mysql'] = array(
            "test_db" => array(
                "username" => "root",
                "password" => "secret",
            ),
        );
        exec('cd ' . __DIR__ . " && mysql -u root -psecret test_db < test_db.sql");
        $this->runApp($config);
        $fileRegex = '/\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_test_db\.sql/';
        $backupDir = $this->backupsDir . '/my_project_1/';
        $this->assertTrue($this->dirContainsRegexFile($backupDir, $fileRegex));
    }

    /**
     * @test
     */
    public function it_does_not_backup_if_configuration_is_wrong()
    {
        $config = $this->getBaseConfig();
        $config['storages']['dropbox'] = array('driver' => 'dropbox', 'username' => 'test@example.com');
        $this->runApp($config);
        $this->assertFalse(is_dir($this->backupsDir . '/my_project_1/'));
    }

    /**
     * @test
     */
    public function it_includes_the_config()
    {
        $path = __DIR__.'/../config/config.php';

        if ( is_file($path)) {
            $this->assertTrue(false, 'Config file already exists. Please delete it before testing.');
        }

        $file = fopen($path, 'w');
        fwrite($file, $this->configFileContents());
        fclose($file);

        include(__DIR__.'/../backup.php');
        $this->assertTrue(isset($config), 'Config could not be included.');
        $this->assertTrue(unlink(realpath($path)), 'Config file could not be deleted.');
    }

    private function configFileContents()
    {
        return "<?php return array(
            'project_defaults' => array(),
            'projects' => array(),
            'storages' => array(),
            'app' => array(
                'timezone' => 'Europe/Berlin',
                'time_limit' => 0,
                'temp_dir' => '".__DIR__ ."/test_temp',
            ),
        );";
    }

    protected function tearDown()
    {
        if (is_dir($this->backupsDir)) exec('rm -rf ' . realpath($this->backupsDir));
    }

    private function dirContainsRegexFile($dir, $regex)
    {
        $files = scandir($dir);
        foreach ($files as $file)
        {
            if (preg_match($regex, $file) === 1)
            {
                return true;
            }
        }
        return false;
    }

}