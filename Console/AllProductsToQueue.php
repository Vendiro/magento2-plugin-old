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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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

namespace TIG\Vendiro\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TIG\Vendiro\Service\Inventory\QueueAll as QueueAllService;

class AllProductsToQueue extends Command
{
    /** @var QueueAllService $queueAllService */
    private $queueAllService;

    /**
     * AllProductsToQueue constructor.
     *
     * @param QueueAllService $queueAllService
     * @param string|null     $name
     */
    public function __construct(
        QueueAllService $queueAllService,
        ?string $name = null
    ) {
        $this->queueAllService = $queueAllService;

        parent::__construct($name);
    }

    // @codingStandardsIgnoreLine
    protected function configure()
    {
        $this->setName('vendiro:inventory:queue-all');
        $this->setDescription('Place every product in the inventory queue.');

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    // @codingStandardsIgnoreLine
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queueingResult = $this->queueAllService->queueAll();

        if ($queueingResult) {
            $output->writeln('Your products have been successfully queued and their stock will be send to Vendiro soon.'
                . ' Depending on your amount of products, this may take a few minutes up to a few hours.');

            return;
        }

        $output->writeln('The products could not be queued for updating the stock at Vendiro.');
    }
}
