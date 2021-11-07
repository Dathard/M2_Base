<?php
declare(strict_types=1);

namespace Dathard\Base\Block\Adminhtml\System\Config\Notifications;

use Dathard\Base\Model\Extension\Data as ExtensionDataModel;
use Dathard\Base\Model\Extension\VersionProvider;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Version extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Dathard_Base::system/config/notifications/version.phtml';

    /**
     * @var ExtensionDataModel
     */
    private $extensionDataModel;

    /**
     * @var VersionProvider
     */
    private $versionProvider;

    /**
     * @param ExtensionDataModel $extensionDataModel
     * @param VersionProvider $versionProvider
     * @param Context $context
     */
    public function __construct(
        ExtensionDataModel  $extensionDataModel,
        VersionProvider     $versionProvider,
        Context             $context
    ) {
        $this->extensionDataModel = $extensionDataModel;
        $this->versionProvider = $versionProvider;

        parent::__construct($context);
    }

    /**
     * @inherit
     */
    public function toHtml()
    {
        if (! $this->getSection()) {
            return '';
        }

        $extensionData = $this->extensionDataModel->getDataByConfigSection($this->getSection());

        if (! $extensionData) {
            return '';
        }

        $currentVersion = $this->versionProvider->getCurrentExtensionVersionByName($extensionData['name']);
        $latestVersion = $this->versionProvider->getLatestVersionByName($extensionData['name']);

        if (! $latestVersion
            || ! $currentVersion
            || ! version_compare($currentVersion, $latestVersion, '<')) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return string
     */
    public function getRequestUrl(): string
    {
        return $this->getUrl(
            'dtbase/extension/changeLogs',
            ['section' => $this->getSection()]
        );
    }
}
