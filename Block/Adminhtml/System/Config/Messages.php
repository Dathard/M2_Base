<?php
declare(strict_types=1);

namespace Dathard\Base\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Messages extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Dathard_Base::system/config/messages.phtml';

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $columns = $this->getRequest()->getParam('website') || $this->getRequest()->getParam('store') ? 5 : 4;

        return $this->_decorateRowHtml(
            $element,
            "<td colspan='{$columns}'>" . $this->toHtml() . '</td>'
        );
    }

    /**
     * @return string
     */
    public function getRequestUrl(): string
    {
        return $this->getUrl(
            'dtbase/extension/notifications',
            ['section' => $this->getForm()->getSectionCode()]
        );
    }
}
