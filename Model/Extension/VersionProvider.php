<?php
declare(strict_types=1);

namespace Dathard\Base\Model\Extension;

use Dathard\Base\Model\Extension\Data as ExtensionDataModel;
use Dathard\Base\Service\GitApiService;
use Magento\Framework\Module\ModuleListInterface;

class VersionProvider
{
    /**
     * @var array[]
     */
    private $cache = [
        'latest_version' => []
    ];

    /**
     * @var Data
     */
    private $extensionDataModel;

    /**
     * @var GitApiService
     */
    private $gitApiService;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param Data $extensionDataModel
     * @param GitApiService $gitApiService
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ExtensionDataModel $extensionDataModel,
        GitApiService $gitApiService,
        ModuleListInterface $moduleList
    ) {
        $this->extensionDataModel = $extensionDataModel;
        $this->gitApiService = $gitApiService;
        $this->moduleList = $moduleList;
    }

    /**
     * @param string $extensionName
     * @return string|null
     */
    public function getLatestVersionByName(string $extensionName)
    {
        if (! array_key_exists('latest_version', $this->cache)
            || ! array_key_exists($extensionName, $this->cache['latest_version'])) {
            $extensionData = $this->extensionDataModel->getExtensionData($extensionName);

            if (! $extensionData) {
                return null;
            }

            $latestReleaseData = $this->gitApiService->getLatestRelease($extensionData['repository_name']);

            if(! $latestReleaseData) {
                return null;
            }

            $version = str_replace("REL-", '', (string) $latestReleaseData['tag_name']);
            $this->cache['latest_version'][$extensionName] = $version;
        }

        return $this->cache['latest_version'][$extensionName];
    }

    /**
     * @param string $extensionName
     * @return string|null
     */
    public function getCurrentExtensionVersionByName(string $extensionName)
    {
        $extensionData = $this->extensionDataModel->getExtensionData($extensionName);

        if (! $extensionData) {
            return null;
        }

        return (string) $this->moduleList
            ->getOne('Dathard_' . $extensionData['name'])['setup_version'];
    }
}
