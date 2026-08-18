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
 * Callback implementations for report_rubricgrading
 *
 * Adds a navigation link to the assignment module menu when the assignment
 * uses rubric advanced grading.
 *
 * @package    report_rubricgrading
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/grade/grading/lib.php');

/**
 * Add a report link to the assignment navigation when rubric grading is active.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param cm_info $cm The course module info object
 * @return void
 */
function report_rubricgrading_extend_navigation_module(navigation_node $navigation, cm_info $cm): void {
    // Is the activity module supported?
    if (!\report_rubricgrading\plugin_manager::is_activity_supported($cm)) {
        return;
    }

    // Does the user have the capability to view the report?
    if (!has_capability('report/rubricgrading:view', $cm->context)) {
        return;
    }

    // Get the rubric grading class from the plugin, to handle how it interacts with the report.
    $plugin = \report_rubricgrading\plugin_manager::load($cm);
    if (!$plugin || !$plugin->is_activity_using_supported_grading_method()) {
        return;
    }

    $url = new moodle_url('/report/rubricgrading/index.php', ['cmid' => $cm->id]);
    $navigation->add(
        get_string('pluginname', 'report_rubricgrading'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'rubricgradingreport'
    );
}
