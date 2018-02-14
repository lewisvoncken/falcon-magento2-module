<?php

namespace Deity\MagentoCmd;

use Composer\Script\Event;
use Composer\Util\ProcessExecutor;

/**
 * Helper Composer command to manage package releases
 * @package deityMagentoApi\Cmd
 */
class Command
{
    const INCREASE_PATCH = 'patch';
    const INCREASE_MINOR = 'minor';
    const INCREASE_MAJOR = 'major';

    /**
     * @param Event $event
     * @throws \Exception
     */
    public static function run(Event $event)
    {
        $io = $event->getIO();
        $processExecutor = new ProcessExecutor($io);
        $currentVersion = $event->getComposer()->getPackage()->getVersion();
        $versionParts = explode('.', $currentVersion);

        // getting rid of "build" version value
        array_pop($versionParts);

        $io->write("Current version: {$currentVersion}");

        $versionIncrease = self::INCREASE_PATCH;
        if(($arguments = $event->getArguments())) {
            $versionIncrease = $arguments[0];
        }

        switch($versionIncrease) {
            case self::INCREASE_PATCH:
                $versionIndex = 2;
                break;
            case self::INCREASE_MINOR:
                $versionIndex = 1;
                break;
            case self::INCREASE_MAJOR:
                $versionIndex = 0;
                break;
            default:
                throw new \Exception("Unknown flag");
        }

        $versionParts[$versionIndex]++;
        for($i = $versionIndex + 1; $i < count($versionParts); $i++) {
            $versionParts[$i] = 0;
        }

        $newVersion = implode('.', $versionParts);
        $io->write("New version: {$newVersion}");

        if($io->askConfirmation('Do you want to apply it? (y/n)')) {
            $jsonFiles = [
                dirname(__DIR__) . '/composer.json',
                dirname(__DIR__) . '/src/composer.json',
            ];

            foreach ($jsonFiles as $jsonFile) {
                $jsonContent = json_decode(file_get_contents($jsonFile), true);
                $jsonContent['version'] = $newVersion;
                file_put_contents($jsonFile, json_encode($jsonContent, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            }

            $xmlFile = dirname(__DIR__) . '/src/etc/module.xml';
            file_put_contents($xmlFile,  preg_replace('/setup_version="(\d.+)"/', 'setup_version="'. $newVersion .'"', file_get_contents($xmlFile)));

            $io->write('Files updated.');

            $output = null;
            $processExecutor->execute(sprintf('git add %s', implode(' ',  array_merge($jsonFiles, [$xmlFile]))));
            $processExecutor->execute(sprintf('git commit -m "%s" && git push origin', $newVersion));
            $processExecutor->execute(sprintf("git tag -a %s -m \"%s\"", $newVersion, $newVersion));
            $processExecutor->execute(sprintf("git push origin %s", $newVersion));

            $io->write('Git tag created.');
        }
    }
}