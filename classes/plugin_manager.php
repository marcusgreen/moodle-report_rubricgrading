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
 * Manager for how different activity module plugins interact with the report.
 *
 * @package    report_rubricgrading
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_rubricgrading;

use cm_info;
use report_rubricgrading\local\mod\assign;
use report_rubricgrading\local\plugin_base;

class plugin_manager {
    /**
     * Get an array of the supported activity module plugins, enabled in the plugin settings.
     * @return string[]
     */
    protected static function get_supported_plugins() {
        return explode(',', get_config('report_rubricgrading', 'supported_plugins'));
    }

    /**
     * Check if the given activity module is supported (enabled in the plugin settings).
     * @param cm_info $cm
     * @return bool
     */
    public static function is_activity_supported(cm_info $cm): bool {
        return in_array($cm->modname, self::get_supported_plugins());
    }

    /**
     * Load the activity module's rubricgrading implementation class.
     * @param cm_info $cm
     * @return plugin_base
     * @throws \coding_exception
     */
    public static function load(cm_info $cm): ?plugin_base {
        // If it's the core assign module, we have built a class for that into the report to avoid core changes.
        if ($cm->modname === 'assign') {
            return new assign($cm);
        } else {
            // Otherwise, we need to find the class inside the activity plugin's directory.
            // Must be: mod_<component>\local\report_rubricgrading\<component>.php
            // and implement report_rubricgrading\local\plugin_base.
            $class = "\\mod_{$cm->modname}\\local\\report_rubricgrading\\{$cm->modname}";
            if (class_exists($class)) {
                return new $class($cm);
            } else {
                return null;
            }
        }
    }
}
