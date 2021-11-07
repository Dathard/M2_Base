<?php
declare(strict_types=1);

namespace Dathard\Base\Controller\Adminhtml\Extension;

use Dathard\Base\Block\Adminhtml\System\Config\Notifications\ChangeLogs as ChangeLogsBlock;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;

class ChangeLogs extends Action
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    public $layoutFactory;

    /**
     * @param Context $context
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $result = [
            'html' => $this->layoutFactory->create()
                ->createBlock(ChangeLogsBlock::class)
                ->setSection($this->getRequest()->getParam('section'))
                ->toHtml()
        ];

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
