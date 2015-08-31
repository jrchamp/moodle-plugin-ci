<?php

/*
 * This file is part of the Moodle Plugin CI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moodlerooms\MoodlePluginCI\Installer;

use Moodlerooms\MoodlePluginCI\Bridge\Moodle;
use Moodlerooms\MoodlePluginCI\Bridge\MoodlePlugin;
use Moodlerooms\MoodlePluginCI\Installer\Database\AbstractDatabase;
use Moodlerooms\MoodlePluginCI\Process\Execute;

/**
 * Installer Factory.
 *
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class InstallerFactory
{
    /**
     * @var Moodle
     */
    public $moodle;

    /**
     * @var MoodlePlugin
     */
    public $plugin;

    /**
     * @var Execute
     */
    public $execute;

    /**
     * @var AbstractDatabase
     */
    public $database;

    /**
     * @var string
     */
    public $branch;

    /**
     * @var string
     */
    public $dataDir;

    /**
     * @var array
     */
    public $notPaths = [];

    /**
     * @var array
     */
    public $notNames = [];

    /**
     * @var bool
     */
    public $includeJS = true;

    /**
     * Given a big bag of install options, add installers to the collection.
     *
     * @param InstallerCollection $installers Installers will be added to this.
     */
    public function addInstallers(InstallerCollection $installers)
    {
        $installers->add(new MoodleInstaller($this->execute, $this->database, $this->moodle, $this->branch, $this->dataDir));
        $installers->add(new PluginInstaller($this->moodle, $this->plugin, $this->notPaths, $this->notNames));

        if ($this->plugin->hasBehatFeatures() || $this->plugin->hasUnitTests()) {
            $installers->add(new ComposerInstaller($this->moodle, $this->execute));
        }
        if ($this->plugin->hasBehatFeatures()) {
            $installers->add(new BehatInstaller($this->moodle, $this->execute));
        }
        if ($this->plugin->hasUnitTests()) {
            $installers->add(new PHPUnitInstaller($this->moodle, $this->execute));
        }
        if ($this->includeJS === true) {
            $installers->add(new JSInstaller($this->execute));
        }
    }
}
