<?php
/**
 * Copyright (c) 2017 Bastian Germann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Cforms2;

/**
 * A Singleton to give access to all settings of cformsII.
 */
class Settings
{

    private static $settings;
    private $cfs;

    private function __construct()
    {
        $this->cfs = get_option('cforms_settings');
    }

    /**
     * Returns a setting. Has variable arguments, which are used as indexes of the settings array.
     */
    public function get()
    {
        $ret = $this->cfs;
        foreach(func_get_args() as $arg) {
            if (!isset($ret[$arg])) {
                return null;
            }
            $ret = $ret[$arg];
        }
        return $ret;
    }

    /**
     * Gives access to this Singleton.
     *
     * @return Settings the Settings Singleton
     */
    public static function instance()
    {
        if (empty(Settings::$settings)) {
            Settings::$settings = new Settings();
        }
        return Settings::$settings;
    }

    /**
     * Resets the static fields.
     */
    public static function reset()
    {
        Settings::$settings = new Settings();
    }
}
