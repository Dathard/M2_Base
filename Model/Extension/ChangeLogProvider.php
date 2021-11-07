<?php
declare(strict_types=1);

namespace Dathard\Base\Model\Extension;

use Dathard\Base\Model\Extension\Data as ExtensionDataModel;
use Dathard\Base\Service\GitApiService;

class ChangeLogProvider
{
    /**
     * @var array[]
     */
    private $cache = [
        'change_logs' => []
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
     * @param Data $extensionDataModel
     * @param GitApiService $gitApiService
     */
    public function __construct(
        ExtensionDataModel $extensionDataModel,
        GitApiService $gitApiService
    ) {
        $this->extensionDataModel = $extensionDataModel;
        $this->gitApiService = $gitApiService;
    }

    /**
     * @param string $extensionName
     * @return array|null
     */
    public function getChangeLogsByExtensionName(string $extensionName)
    {
        if (! array_key_exists('change_logs', $this->cache)
            || ! array_key_exists($extensionName, $this->cache['change_logs'])) {
            $extensionData = $this->extensionDataModel->getExtensionData($extensionName);

            if (! $extensionData) {
                return null;
            }

            $changeLogsData = $this->gitApiService->getReleasesList($extensionData['repository_name']);

            if(! $changeLogsData) {
                return null;
            }

            $this->cache['change_logs'][$extensionName] = $changeLogsData;
        }

        return $this->cache['change_logs'][$extensionName];
    }
}
