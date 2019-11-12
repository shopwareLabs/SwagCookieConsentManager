<?php

namespace SwagCookieConsentManager;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use SwagCookieConsentManager\Bootstrap\Installer;
use SwagCookieConsentManager\Bootstrap\Uninstaller;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class SwagCookieConsentManager extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator($this->getPath() . '/Bundle/'));
        $loader->load('CookieBundle/DependencyInjection/services.xml');

        $container->removeDefinition('shopware.components.privacy.cookie_remove_subscriber');
        parent::build($container);
    }

    public function install(InstallContext $context): void
    {
        $installer = new Installer($context, $this->container->get('dbal_connection'));
        $installer->install();
    }

    public function activate(ActivateContext $context): void
    {
        $context->scheduleClearCache([
            InstallContext::CACHE_TAG_CONFIG,
            InstallContext::CACHE_TAG_HTTP,
            InstallContext::CACHE_TAG_TEMPLATE,
            InstallContext::CACHE_TAG_THEME,
        ]);
    }

    public function uninstall(UninstallContext $context)
    {
        $uninstaller = new Uninstaller($context, $this->container->get('dbal_connection'));
        $uninstaller->uninstall();

        $context->scheduleClearCache([
            InstallContext::CACHE_TAG_CONFIG,
            InstallContext::CACHE_TAG_HTTP,
            InstallContext::CACHE_TAG_TEMPLATE,
            InstallContext::CACHE_TAG_THEME,
        ]);
    }
}