<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Location_folio extends MY_Migration
{

public function up()
{
$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230905146090_location_folio.sql'));
}

public function down()
{
}
}
