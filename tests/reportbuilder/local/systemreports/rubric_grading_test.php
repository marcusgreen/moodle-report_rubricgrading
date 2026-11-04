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
 * Tests for the rubric_grading system report column hooks.
 *
 * @package    report_rubricgrading
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace report_rubricgrading\reportbuilder\local\systemreports;

use advanced_testcase;
use cm_info;
use core_reportbuilder\local\report\column;
use lang_string;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;
use ReflectionMethod;
use report_rubricgrading\local\mod\assign;
use report_rubricgrading\local\plugin_base;
use stdClass;

/**
 * Tests for plugin_base::alter_column() as used by rubric_grading::define_columns().
 */
#[CoversClass(rubric_grading::class)]
final class rubric_grading_test extends advanced_testcase {
    public function test_alter_column_called_for_every_core_column_with_criterion(): void {
        $this->resetAfterTest();

        $criterion = $this->get_criterion();
        $plugin = $this->get_plugin(static function (string $name, column $column, ?stdClass $crit): void {
        });
        $this->define_columns($plugin, [$criterion]);

        $this->assertSame([
            'student_fullname',
            'crit1_score',
            'crit1_remark',
            'overallfeedback',
            'grade',
            'grader_fullname',
            'timegraded',
        ], array_keys($plugin->seen));
        $this->assertSame($criterion, $plugin->seen['crit1_score']);
        $this->assertSame($criterion, $plugin->seen['crit1_remark']);
        $this->assertNull($plugin->seen['grade']);
    }

    public function test_core_formatting_kept_when_plugin_does_nothing(): void {
        $this->resetAfterTest();

        $plugin = $this->get_plugin(static function (string $name, column $column, ?stdClass $crit): void {
        });
        $columns = $this->define_columns($plugin, [$this->get_criterion()]);

        $this->assertSame(format_float(75.5, 2), $this->format($columns['grade'], 75.5));
        $this->assertSame(format_float(3, 2), $this->format($columns['crit1_score'], 3));
        $this->assertSame('', $this->format($columns['grade'], null));
    }

    public function test_added_callback_runs_after_core_formatting(): void {
        $this->resetAfterTest();

        $plugin = $this->get_plugin(static function (string $name, column $column, ?stdClass $crit): void {
            if ($name === 'grade') {
                $column->add_callback(static fn(string $value): string => "[{$value}]");
            }
        });
        $columns = $this->define_columns($plugin, [$this->get_criterion()]);

        $this->assertSame('[' . format_float(75.5, 2) . ']', $this->format($columns['grade'], 75.5));
        $this->assertSame(format_float(3, 2), $this->format($columns['crit1_score'], 3));
    }

    public function test_set_callback_replaces_core_formatting(): void {
        $this->resetAfterTest();

        $plugin = $this->get_plugin(static function (string $name, column $column, ?stdClass $crit): void {
            if ($name === 'crit1_score') {
                $column->set_callback(static fn(?float $value): string => 'hidden');
            }
        });
        $columns = $this->define_columns($plugin, [$this->get_criterion()]);

        $this->assertSame('hidden', $this->format($columns['crit1_score'], 3));
        $this->assertSame(format_float(75.5, 2), $this->format($columns['grade'], 75.5));
    }

    /**
     * Get an assign plugin whose alter_column() records each call and delegates to $alter.
     *
     * @param callable $alter Receives (name, column, criterion).
     * @return plugin_base
     */
    private function get_plugin(callable $alter): plugin_base {
        return new class ($this->get_assign_cm_info(), $alter) extends assign {
            /** @var array Criterion passed for each column name, in call order. */
            public array $seen = [];

            /** @var callable */
            private $alter;

            /**
             * Construct the stub plugin.
             * @param cm_info $cm
             * @param callable $alter
             */
            public function __construct(cm_info $cm, callable $alter) {
                parent::__construct($cm);
                $this->alter = $alter;
            }

            /**
             * Record the call, then delegate to the test's callback.
             * @param string $name
             * @param column $column
             * @param string $alias
             * @param stdClass|null $criterion
             * @return void
             */
            public function alter_column(string $name, column $column, string $alias, ?stdClass $criterion = null): void {
                $this->seen[$name] = $criterion;
                ($this->alter)($name, $column, $criterion);
            }
        };
    }

    /**
     * Run rubric_grading::define_columns() without building the pivot table.
     *
     * @param plugin_base $plugin
     * @param array $criteria
     * @return column[] Report columns keyed by column name.
     */
    private function define_columns(plugin_base $plugin, array $criteria): array {
        $report = (new ReflectionClass(rubric_grading::class))->newInstanceWithoutConstructor();

        $annotate = new ReflectionMethod($report, 'annotate_entity');
        $entity = (new ReflectionClass(rubric_grading::class))->getConstant('ENTITY');
        $annotate->invoke($report, $entity, new lang_string('pluginname', 'report_rubricgrading'));

        $define = new ReflectionMethod($report, 'define_columns');
        $define->invoke($report, 'rp', $criteria, $plugin);

        $columns = [];
        foreach ($report->get_columns() as $column) {
            $columns[$column->get_name()] = $column;
        }
        return $columns;
    }

    /**
     * Format a value through a column's callbacks, as the report table would.
     *
     * @param column $column
     * @param mixed $value Raw value of the column's first field.
     * @return mixed
     */
    private function format(column $column, $value) {
        $fields = $column->get_fields();
        $alias = substr($fields[0], strrpos($fields[0], ' AS ') + 4);

        return $column->format_value([$alias => $value]);
    }

    /**
     * Get a criterion as returned by rubric_grading::get_criteria().
     * @return stdClass
     */
    private function get_criterion(): stdClass {
        return (object)['id' => 5, 'description' => 'Grammar', 'sortorder' => 0];
    }

    /**
     * Create an assignment and return its course module.
     * @return cm_info
     */
    private function get_assign_cm_info(): cm_info {
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        $modinfo = get_fast_modinfo($course);
        return $modinfo->get_cm($assign->cmid);
    }
}
