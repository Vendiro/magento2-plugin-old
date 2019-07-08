<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\Vendiro\Block\Adminhtml\Config\Support;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use TIG\Vendiro\Model\Config\Provider\Support\Tab as SupportTab;
use TIG\Vendiro\Service\Software\Data as SoftwareData;

class Tab extends Template implements RendererInterface
{
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_Vendiro::config/support/tab.phtml';

    /** @var SupportTab */
    private $supportTab;

    /** @var SoftwareData */
    private $softwareData;

    /**
     * Tab constructor.
     *
     * @param Template\Context         $context
     * @param SupportTab               $supportTab
     * @param SoftwareData             $softwareData
     * @param array                    $data
     */
    public function __construct(
        Template\Context $context,
        SupportTab $supportTab,
        SoftwareData $softwareData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->supportTab           = $supportTab;
        $this->softwareData         = $softwareData;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    // @codeCoverageIgnoreStart
    public function render(AbstractElement $element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setElement($element);

        return $this->toHtml();
    }
    // @codeCoverageIgnoreEnd

    /**
     * Retrieve the version number from the database.
     *
     * @return bool|false|string
     */
    public function getVersionNumber()
    {
        return $this->softwareData->getModuleVersion();
    }

    /**
     * @return string
     */
    public function getSupportedMagentoVersions()
    {
        return $this->supportTab->getSupportedMagentoVersions();
    }

    /**
     * @return bool|int
     */
    public function phpVersionCheck()
    {
        return $this->softwareData->phpVersionIsSupported();
    }

    /**
     * @return array|bool
     */
    public function getPhpVersion()
    {
        return $this->softwareData->getPhpVersion();
    }

    /**
     * @return array|bool
     */
    public function getMagentoVersionTidyString()
    {
        return $this->softwareData->getMagentoVersion();
    }
}
