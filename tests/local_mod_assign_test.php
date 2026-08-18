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
 * Tests for assign rubricgrading implementation.
 *
 * @package    report_rubricgrading
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace report_rubricgrading;

use advanced_testcase;
use cm_info;
use ReflectionMethod;
use report_rubricgrading\local\mod\assign;

final class local_mod_assign_test extends advanced_testcase {
    public function test_get_row_key_uses_userid(): void {
        $this->resetAfterTest();

        $plugin = new assign($this->get_assign_cm_info());

        $this->assertSame(42, $plugin->get_row_key((object)['userid' => 42]));
    }

    private function get_assign_cm_info(): cm_info {
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        $modinfo = get_fast_modinfo($course);
        return $modinfo->get_cm($assign->cmid);
    }

    private function invoke_protected(object $instance, string $methodname): string {
        $method = new ReflectionMethod($instance, $methodname);
        $method->setAccessible(true);

        return (string)$method->invoke($instance);
    }
}

