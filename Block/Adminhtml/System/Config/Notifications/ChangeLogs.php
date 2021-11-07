<?php
declare(strict_types=1);

namespace Dathard\Base\Block\Adminhtml\System\Config\Notifications;

use Dathard\Base\Model\Extension\ChangeLogProvider;
use Dathard\Base\Model\Extension\Data as ExtensionDataModel;
use Dathard\Base\Model\Extension\VersionProvider;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class ChangeLogs extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Dathard_Base::system/config/notifications/change-logs.phtml';

    private $extensionData;

    private $currentExtensionVersion;

    /**
     * @var ExtensionDataModel
     */
    private $extensionDataModel;

    /**
     * @var ChangeLogProvider
     */
    private $changeLogProvider;

    /**
     * @var VersionProvider
     */
    private $versionProvider;

    /**
     * @param ChangeLogProvider $changeLogProvider
     * @param ExtensionDataModel $extensionDataModel
     * @param VersionProvider $versionProvider
     * @param Context $context
     */
    public function __construct(
        ChangeLogProvider $changeLogProvider,
        ExtensionDataModel   $extensionDataModel,
        VersionProvider $versionProvider,
        Context         $context
    ) {
        $this->extensionDataModel = $extensionDataModel;
        $this->changeLogProvider = $changeLogProvider;
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

        $this->extensionData = $this->extensionDataModel->getDataByConfigSection($this->getSection());

        if (! $this->extensionData) {
            return '';
        }

        $this->currentExtensionVersion = $this->versionProvider->getCurrentExtensionVersionByName($this->extensionData['name']);
        $latestVersion = $this->versionProvider->getLatestVersionByName($this->extensionData['name']);

        if (! $latestVersion
            || ! $this->currentExtensionVersion
            || ! version_compare($this->currentExtensionVersion, $latestVersion, '<')) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return array|null
     */
    public function getChangeLogs()
    {
        if (! $this->getSection()
            && ! $this->extensionData) {
            return [];
        }

        if (! $this->extensionData) {
            $this->extensionData = $this->extensionDataModel->getDataByConfigSection($this->getSection());
        }

        if (! $this->extensionData) {
            return [];
        }

        $changeLogs = $this->changeLogProvider->getChangeLogsByExtensionName($this->extensionData['name']);

        if (! $changeLogs) {
            return [];
        }

        return $changeLogs;
    }

    /**
     * @param array $changeLog
     * @return bool
     */
    public function isNewerVersion($changeLog = []): bool
    {
        if (!array_key_exists('tag_name', $changeLog)) {
            return false;
        }

        if (! $this->currentExtensionVersion) {
            $this->currentExtensionVersion = $this->versionProvider->getCurrentExtensionVersionByName($this->extensionData['name']);
        }

        $version = str_replace("REL-", '', (string) $changeLog['tag_name']);

        return (bool) version_compare($version, $this->currentExtensionVersion, '>');
    }

    /**
     * @param array $changeLog
     * @return string
     */
    public function getVersion(array $changeLog = []): string
    {
        if (! array_key_exists('tag_name', $changeLog)) {
            return '';
        }

        return (string) str_replace("REL-", '', (string) $changeLog['tag_name']);
    }

    /**
     * @param array $changeLog
     * @return string
     */
    public function getPublishedDate(array $changeLog = []): string
    {
        if (!array_key_exists('published_at', $changeLog)) {
            return '';
        }

        $publishedDate = $changeLog['published_at'];

        return (string) date('M d, Y', strtotime($publishedDate));
    }

    /**
     * @param array $changeLog
     * @return string
     */
    public function getChangeDescription(array $changeLog = []): string
    {
        if (!array_key_exists('body', $changeLog)) {
            return '';
        }

        return str_ireplace(array("\r\n", "\n", "\r"), '<br />', $changeLog['body']);;
    }
}
