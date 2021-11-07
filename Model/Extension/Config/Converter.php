<?php
declare(strict_types=1);

namespace Dathard\Base\Model\Extension\Config;

use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Data\Argument\Interpreter\Boolean as BooleanInterpreter;

class Converter implements ConverterInterface
{
    const FIELD_CONFIG_SECTION = 'config_section';
    const REPOSITORY_NAME = 'repository_name';

    /**
     * @var BooleanInterpreter
     */
    private $booleanInterpreter;

    /**
     * @param BooleanInterpreter $booleanInterpreter
     */
    public function __construct(
        BooleanInterpreter $booleanInterpreter
    ) {
        $this->booleanInterpreter = $booleanInterpreter;
    }

    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = [];
        /** @var \DOMNodeList $extensions */
        $extensions = $source->getElementsByTagName('extension');
        /** @var \DOMNode $extension */
        foreach ($extensions as $extension) {
            $extensionConfig = [
                self::FIELD_CONFIG_SECTION => '',
                self::REPOSITORY_NAME => ''
            ];
            /** @var \DOMAttr $attribute */
            foreach ($extension->attributes as $attribute) {
                $value = $attribute->nodeValue;
                $extensionConfig[$attribute->nodeName] = $value;
            }

            $output[$extension->attributes->getNamedItem('name')->nodeValue] = $extensionConfig;
        }

        return $output;
    }
}
