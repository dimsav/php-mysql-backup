<?php

use Dimsav\Backup\Project;

class BackupsTest extends PHPUnit_Framework_TestCase {

    /** @var \Dimsav\Backup\Config */
    private $config;

    public function setUp()
    {
        $this->config = new \Dimsav\Backup\Config('testing');
    }

    public function testConfigDetermination()
    {
        $this->assertSame(
            $this->config->get('projects.default.password'),
            'testing-default-secret');
    }

    public function testProjectDeterminationFromConfig()
    {
        $project = new Project($this->config, 'test-1');

        $this->assertSame($project->getName(), "test-1");
        $this->assertSame(
            $project->getPaths(),
            array(
                realpath(__DIR__."/../../../src"),
            )
        );
        $this->assertSame(
            $project->getExcludes(),
            array(
                realpath(__DIR__."/../../../src/Dimsav/Backup/Config.php"),
            )
        );
    }

    public function testProjectDefaults()
    {
        $project = new Project($this->config, 'test-3');

        $this->assertSame($project->getDbHost(), "localhost");
        $this->assertSame($project->getDbPort(), "3306");
        $this->assertSame($project->getDbUsername(), "root");
        $this->assertSame($project->getDbPassword(), "password");
    }

    public function testProjectOverridingDefaults()
    {
        $project = new Project($this->config, 'test-1');
        $this->assertSame($project->getPassword(), null);
    }

    public function testGetAllProjects()
    {
        $repo = new ProjectRepository($this->config);
        $projects = $repo->all();

        $this->assertEquals(count($this->config->get('projects.projects')), count($projects));
        $this->assertInstanceOf('Dimsav\Backup\Project', $projects[0]);
    }

    public function testBasePathInProjectOverridesTheDefault()
    {
        $project = new Project($this->config, 'test-1');
        $this->assertEquals(realpath(__DIR__."/../../../"), $project->getBasePath());
        $project = new Project($this->config, 'test-2');
        $this->assertEquals(realpath(__DIR__."/../../../src"), $project->getBasePath());
    }
}