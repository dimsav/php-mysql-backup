<?php

return array(

    "default" => array(
        "base-path" => realpath(__DIR__."/../../"),
        "database" => array(
            "host" => "localhost",
            "port" => "3306",
            "username" => "root",
            "password" => "password",
        ),
        "password" => "testing-default-secret"
    ),

    "projects" => array(

        "test-1" => array(

            "paths" => array(
                realpath(__DIR__."/../../src"),
            ),
            "excludes" => array(
                realpath(__DIR__."/../../src/Dimsav/Backup/Config.php"),
            ),
            "password" => null,
        ),
        "test-2" => array(
            "base-path" => realpath(__DIR__."/../../src"),
            "paths" => array('Dimsav'),
        ),
        "test-3" => array(
            "database" => array(
                "name"    =>"test_3",
            ),
        )
    ),

    "storages" => array(

        "dropbox1" => array(
            "type" => "dropbox",
            "username" => "email",
            "password" => "password",
            "destination" => "Backups"
        ),

        "dropbox2" => array(
            "type" => "dropbox",
            "username" => "email",
            "password" => "password",
            "destination" => "Backups"
        ),

    )

);
