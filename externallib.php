<?php

use core_completion\progress;
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/course/lib.php');

class local_custom_service_external extends external_api {

    public static function get_pag_parameters() {
        return new external_function_parameters(
            array(
                'page' => new external_value(PARAM_INT, 'Número de página', false, 1),
                'perpage' => new external_value(PARAM_INT, 'Número de cursos por página', false, 10),
            )
        );
    }

    public static function get_pag($page = 1, $perpage = 10) {
        global $DB, $USER;

        if (!isloggedin() || isguestuser()) {
            throw new moodle_exception('nologin', 'error');
        }

        $context = context_system::instance();
        require_capability('moodle/course:view', $context);

        $page = (int) $page;
        $perpage = (int) $perpage;

        if ($page < 1) {
            $page = 1;
        }

        if ($perpage < 1 || $perpage > 100) {
            $perpage = 10;
        }

        $limitfrom = ($page - 1) * $perpage;
        $cursos = $DB->get_records('course', null, '', '*', $limitfrom, $perpage);
        
        $response = array();
        foreach ($cursos as $curso) {
            $course = new stdClass();
            $course->id = $curso->id;
            $course->fullname = $curso->fullname;
            $course->shortname = $curso->shortname ?? '';
            $course->summary = $curso->summary ?? '';
            $course->startdate = $curso->startdate ?? '';
            $course->enddate = $curso->enddate ?? '';
            $course->category = $curso->category ?? '';
            $response[] = $course;
        }

        $total = $DB->count_records('course');

        $total_pages = ceil($total / $perpage);

        $return = new stdClass();
        $return->total = $total;
        $return->page = $page;
        $return->per_page = $perpage;
        $return->total_pages = $total_pages;
        $return->data = $response;

        return $return;
    }

    public static function get_pag_returns() {
        return new external_single_structure(
            array(
                'total' => new external_value(PARAM_INT, 'Total de cursos'),
                'page' => new external_value(PARAM_INT, 'Número de página'),
                'per_page' => new external_value(PARAM_INT, 'Número de cursos por página'),
                'total_pages' => new external_value(PARAM_INT, 'Total de páginas'),
                'data' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'ID del curso'),
                            'fullname' => new external_value(PARAM_TEXT, 'Nombre completo del curso'),
                            'shortname' => new external_value(PARAM_TEXT, 'Nombre corto del curso'),
                            'summary' => new external_value(PARAM_TEXT, 'Resumen del curso'),
                            'startdate' => new external_value(PARAM_TEXT, 'Fecha de inicio del curso'),
                            'enddate' => new external_value(PARAM_TEXT, 'Fecha de finalización del curso'),
                            'category' => new external_value(PARAM_TEXT, 'Categoría del curso'),
                        )
                    )
                )
            )
        );
    }
}
