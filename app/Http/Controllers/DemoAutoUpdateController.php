<?php

namespace App\Http\Controllers;

class DemoAutoUpdateController extends Controller
{
    /*************************************************
    *
    *   Developer Controll API || Demo
    *
    **************************************************/

    private $demo_version;
    private $minimum_required_version;
    private $latest_version_db_migrate_enable;
    private $version_upgrade_file_url;

    public function __construct()
    {
        $this->demo_version = env('VERSION');
        $this->minimum_required_version = env('MINIMUM_REQUIRED_VERSION');
        $this->latest_version_db_migrate_enable= env('LATEST_VERSION_DB_MIGRATE_ENABLE');
        $this->version_upgrade_file_url = env('VERSION_UPGRADE_FILE_URL');
    }


    public function isUpdateAvailable()
    {
        $data = [
                'demo_version'              => $this->demo_version,
                'minimum_required_version'  => $this->minimum_required_version,
                'latest_version_db_migrate_enable' => $this->latest_version_db_migrate_enable,
                'version_upgrade_file_url' => $this->version_upgrade_file_url

        ];
        return response()->json($data,201);
    }
}
