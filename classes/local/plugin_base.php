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
 * Base interface for the plugin implementations of rubricgrading support to implement.
 *
 * @package    report_rubricgrading
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_rubricgrading\local;

use cm_info;
use grading_manager;
use report_rubricgrading\reportbuilder\local\systemreports\rubric_grading;
use stdClass;
use xmldb_table;

abstract class plugin_base {
    /**
     * @var cm_info Activity course module instance.
     */
    protected cm_info $cm;

    /**
     * @var grading_manager Grading manager object.
     */
    protected grading_manager $gradingmanager;

    /**
     * Construct the object.
     * @param cm_info $cm
     */
    public function __construct(cm_info $cm) {
        $this->cm = $cm;
        $this->load_grading_manager();
    }

    /**
     * Get the grading manager.
     * @return grading_manager
     */
    public function get_grading_manager(): grading_manager {
        return $this->gradingmanager;
    }

    /**
     * Get the course module object.
     * @return cm_info
     */
    public function get_cm(): cm_info {
        return $this->cm;
    }

    /**
     * Load the grading manager object for this activity type.
     * @return void
     */
    abstract protected function load_grading_manager(): void;

    /**
     * Check if the given activity instance is using a supported grading method (rubric, ranged rubric, marking guide).
     * @return bool
     */
    public function is_activity_using_supported_grading_method(): bool {
        return (in_array($this->gradingmanager->get_active_method(), [
            'rubric',
            'rubric_ranges',
            'guide',
        ]));
    }

    /**
     * Whether the active grading method exposes a per-criterion "level definition"
     * (rubric and ranged rubric do; marking guide does not).
     *
     * @return bool
     */
    public function method_has_level_definitions(): bool {
        return in_array($this->gradingmanager->get_active_method(), [
            'rubric',
            'rubric_ranges',
        ], true);
    }

    /**
     * Get the SQL for the temp table.
     * @return string
     */
    public function get_sql(): string {
        $method = 'get_sql_' . $this->gradingmanager->get_active_method();
        return $this->$method();
    }

    /**
     * Get the SQL if the grading method is Rubric.
     * @return string
     */
    abstract protected function get_sql_rubric(): string;

    /**
     * Get the SQL if the grading method is Rubric Ranges.
     * @return string
     */
    abstract protected function get_sql_rubric_ranges(): string;

    /**
     * Get the sql if the grading method is Marking Guide.
     * @return string
     */
    abstract protected function get_sql_guide(): string;

    /**
     * Add any extra fields we need to the temp table.
     * @return void
     */
    public function add_report_fields(xmldb_table $xmldbtable): void {
        // Override in activity specific implementation.
    }

    /**
     * Add any extra keys we need to the temp table.
     * @return void
     */
    public function add_report_keys(xmldb_table $xmldbtable): void {
        // Override in activity specific implementation.
    }

    /**
     * Add any extra data to the pivotrow to be stored in the temp table.
     * @param stdClass $row
     * @param stdClass $pivotrow
     * @return void
     */
    public function add_row_data(stdClass $row, stdClass &$pivotrow): void {
        // Override in activity specific implementation.
    }

    /**
     * Add any extra columns to the report to be rendered.
     * This should return an array with the elements: [name, title, type].
     * @return array
     */
    public function add_report_columns(): array {
        // Override in activity specific implementation.
        return [];
    }

    /**
     * Given a row, return what the unique key should be, to avoid multiple rows per set of data.
     * @param stdClass $row
     * @return mixed
     */
    abstract public function get_row_key(stdClass $row): mixed;

}
