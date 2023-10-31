<?php
defined('MOODLE_INTERNAL') || die();

$functions = array(
    'update_courses_lti' => array(
        'classname' => 'local_custom_service_external',
        'methodname' => 'get_pag',
        'classpath' => 'local/custom_service/externallib.php',
        'description' => 'Update courses sections title in DB',
        'type' => 'write',
        'ajax' => true,
    )
);

$services = array(
    'Custom_Service' => array(
        'functions' => array('update_courses_lti'),
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);
