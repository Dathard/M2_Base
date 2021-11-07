<?php
declare(strict_types=1);

namespace Dathard\Base\Model\Extension;

use Dathard\Base\Model\Extension\Data as ExtensionDataModel;
use Dathard\Base\Service\GitApiService;

class ChangeLogProvider
{
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
        $extensionData = $this->extensionDataModel->getExtensionData($extensionName);

        if (! $extensionData) {
            return null;
        }

        $releaseData = $this->gitApiService->getReleasesList($extensionData['repository_name']);

        if(! $releaseData) {
            return null;
        }

        return $releaseData;
    }
}
