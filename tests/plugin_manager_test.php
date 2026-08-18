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
 * Tests for plugin_manager.
 *
 * @package    report_rubricgrading
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace report_rubricgrading;

use advanced_testcase;
use cm_info;
use coding_exception;
use report_rubricgrading\local\mod\assign;

final class plugin_manager_test extends advanced_testcase {
    public function test_is_activity_supported_uses_plugin_config(): void {
        $this->resetAfterTest();

        $cm = $this->get_cm_info('assign');

        set_config('supported_plugins', 'forum,assign', 'report_rubricgrading');
        $this->assertTrue(plugin_manager::is_activity_supported($cm));

        set_config('supported_plugins', 'forum', 'report_rubricgrading');
        $this->assertFalse(plugin_manager::is_activity_supported($cm));
    }

    public function test_load_returns_core_assign_implementation(): void {
        $this->resetAfterTest();

        $cm = $this->get_cm_info('assign');
        $plugin = plugin_manager::load($cm);

        $this->assertInstanceOf(assign::class, $plugin);
    }

    public function test_load_throws_for_missing_plugin_class(): void {
        $this->resetAfterTest();

        $cm = $this->get_cm_info('forum');

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Class \\mod_forum\\local\\report_rubricgrading\\forum not found.');

        plugin_manager::load($cm);
    }

    private function get_cm_info(string $modname): cm_info {
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module($modname, ['course' => $course->id]);

        $modinfo = get_fast_modinfo($course);
        return $modinfo->get_cm($module->cmid);
    }
}

