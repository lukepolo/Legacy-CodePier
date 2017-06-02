<?php

namespace App\Contracts;

use App\Models\Server\Server;

interface RemoteTaskServiceContract
{
    /** runs a command or an array of commands on the target machine
     * @param string | array $commands
     * @param bool $read
     * @param bool $expectedFailure
     * @return string | array - returns the output of the command(s)
     */
    public function run($commands, bool $read = false, bool $expectedFailure = false);

    /**
     * @param Server $server
     */
    public function connect(Server $server): void;

    /** writes to a file on a target machine
     * @param $file
     * @param $contents
     * @param bool $read
     * @return mixed
     */
    public function writeToFile($file, $contents, $read = false);

    /**
     * @param $file
     * @param $text
     *
     * @return string
     */
    public function appendTextToFile($file, $text);

    /**
     * @param $file
     * @param $findText
     * @param $text
     *
     * @return string
     */
    public function findTextAndAppend($file, $findText, $text);

    /**
     * @param $file
     * @param $text
     *
     * @return string
     */
    public function removeLineByText($file, $text);

    /**
     * @param $directory
     *
     * @return string
     */
    public function makeDirectory($directory);

    /**
     * @param $directory
     *
     * @return string
     */
    public function removeDirectory($directory);

    /**
     * @param $file
     *
     * @return string
     */
    public function removeFile($file);

    /**
     * @param $file
     * @param $text
     * @param $replaceWithText
     * @return string
     */
    public function updateText($file, $text, $replaceWithText);

    /**
     * Checks to see if the server has the file.
     * @param $file
     * @return string
     */
    public function hasFile($file);

    /**
     * Checks to see if the server has the file.
     * @param $directory
     * @return string
     */
    public function hasDirectory($directory);
}
