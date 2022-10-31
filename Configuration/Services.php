<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use T3\Mbox\Command\CreateAndSendTestMailsCommand;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
        ->get('mbox');

    if ($extensionConfiguration['debugMode'] ?? false) {
        $container->services()->set(CreateAndSendTestMailsCommand::class)
            ->tag('console.command', [
                'command' => 'mbox:testmails:send',
                'schedulable' => false,
                'hidden' => false,
            ]);
    }
};
