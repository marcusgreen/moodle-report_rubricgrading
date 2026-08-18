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
 * Data to control defaults when creating and running a question
 *
 * @package    report_rubricgrading
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\plugin_manager;

if ($ADMIN->fulltree) {

    // Find all installed plugins which support rubric grading and want this report to work.
    $choices = [];
    $pluginswithfunction = get_plugins_with_function('supports_report_rubricgrading');
    foreach ($pluginswithfunction as $type => $plugins) {
        foreach ($plugins as $plugin => $function) {
            if ($function() === true) {
                $choices[$plugin] = get_string('pluginname', $plugin);
            }
        }
    }

    // Add core assign, as that's always supported.
    $choices['assign'] = get_string('pluginname', 'assign');

    asort($choices);

    $settings->add(new admin_setting_configmulticheckbox(
        'report_rubricgrading/supported_plugins',
        new lang_string('supportedplugins', 'report_rubricgrading'),
        new lang_string('supportedplugins_desc', 'report_rubricgrading'),
        ['mod_assign' => 1],
        $choices,
    ));
}
