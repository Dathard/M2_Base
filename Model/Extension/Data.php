<?php
declare(strict_types=1);

namespace Dathard\Base\Model\Extension;

use Magento\Framework\Config\DataInterface;

class Data
{
    /**
     * @var DataInterface
     */
    private $extensionsConfig;

    /**
     * @param DataInterface $extensionsConfig
     */
    public function __construct(
        DataInterface $extensionsConfig
    ) {
        $this->extensionsConfig = $extensionsConfig;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = $this->extensionsConfig->get();

        return is_array($data) ? $data : [];
    }

    public function getExtensionData(string $extension)
    {
        return $this->extensionsConfig->get($extension);
    }

    /**
     * @param string $section
     * @return array|null
     */
    public function getDataByConfigSection(string $section)
    {
        foreach ($this->getData() as $extensionData) {
            if ($extensionData['config_section'] === $section) {
                return $extensionData;
            }
        }

        return null;
    }
}
