<?php

declare(strict_types=1);

namespace Infrangible\Whoops\Controller\Throwable;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\State;
use Magento\Framework\Controller\Result\ForwardFactory;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Index implements ActionInterface
{
    /** @var State */
    protected $state;

    /** @var ForwardFactory */
    protected $forwardFactory;

    public function __construct(State $state, ForwardFactory $forwardFactory)
    {
        $this->state = $state;
        $this->forwardFactory = $forwardFactory;
    }

    /**
     * @throws \Error
     */
    public function execute()
    {
        if ($this->state->getMode() === State::MODE_DEVELOPER) {
            throw new \Error('Some error occurred');
        } else {
            return $this->forwardFactory->create()->forward('noroute');
        }
    }
}
