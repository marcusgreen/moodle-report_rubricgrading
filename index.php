<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Display rubric grading report for an assignment.
 *
 * Accessed via mod/assign/view.php navigation when the assignment uses
 * rubric advanced grading.
 *
 * @package    report_rubricgrading
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

use core_reportbuilder\system_report_factory;
use report_rubricgrading\reportbuilder\local\systemreports\rubric_grading;

$cmid = required_param('cmid', PARAM_INT);

[$course, $cm] = get_course_and_cm_from_cmid($cmid);
$context = context_module::instance($cmid);

require_login($course, false, $cm);
require_capability('report/rubricgrading:view', $context);

$url = new moodle_url('/report/rubricgrading/index.php', ['cmid' => $cmid]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'report_rubricgrading'));
$PAGE->set_heading($course->fullname);

$returnurl = new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cmid]);
$PAGE->navbar->add($cm->name, $returnurl);
$PAGE->navbar->add(get_string('pluginname', 'report_rubricgrading'));

$PAGE->requires->js_call_amd('report_rubricgrading/pivot_header', 'init');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_rubricgrading'));

$report = system_report_factory::create(
    rubric_grading::class,
    $context,
    'report_rubricgrading',
    '',
    0,
    ['cmid' => $cmid],
);

echo html_writer::tag('style', '#rubricpivot-table-wrap td, #rubricpivot-table-wrap th { vertical-align: top !important; }');
echo html_writer::start_div('', ['id' => 'rubricpivot-table-wrap']);
echo $report->output();
echo html_writer::end_div();

echo $OUTPUT->footer();
