<?php

declare(strict_types=1);

namespace Infrangible\Whoops\Controller\Fatal;

use Magento\Catalog\Model\ProductFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
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
    /** @var ProductFactory */
    protected $productFactory;

    /** @var \Magento\Catalog\Model\ResourceModel\ProductFactory */
    protected $productResourceFactory;

    /** @var State */
    protected $state;

    /** @var ForwardFactory */
    protected $forwardFactory;

    public function __construct(
        ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productResourceFactory,
        State $state,
        ForwardFactory $forwardFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productResourceFactory = $productResourceFactory;
        $this->state = $state;
        $this->forwardFactory = $forwardFactory;
    }

    public function execute()
    {
        if ($this->state->getMode() === State::MODE_DEVELOPER) {
            do {
                $product = $this->productFactory->create();
                $this->productResourceFactory->create()->load(
                    $product,
                    68
                );
                $typeInstance = $product->getTypeInstance();
                if ($typeInstance instanceof Configurable) {
                    $typeInstance->getUsedProducts($product);
                }
            } while (true);
        } else {
            return $this->forwardFactory->create()->forward('noroute');
        }
    }
}
