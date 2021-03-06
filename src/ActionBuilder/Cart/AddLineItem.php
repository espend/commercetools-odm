<?php

namespace BestIt\CommercetoolsODM\ActionBuilder\Cart;

use BestIt\CommercetoolsODM\ActionBuilder\ActionBuilderAbstract;
use BestIt\CommercetoolsODM\Mapping\ClassMetadataInterface;
use Commercetools\Core\Model\Cart\Cart;
use Commercetools\Core\Model\Channel\ChannelReference;
use Commercetools\Core\Request\AbstractAction;
use Commercetools\Core\Request\Carts\Command\CartAddLineItemAction;

/**
 * Builds the action to add cart item
 * @author chowanski <chowanski@bestit-online.de>
 * @package BestIt\CommercetoolsODM
 * @subpackage ActionBuilder\Cart
 * @version $id$
 */
class AddLineItem extends ActionBuilderAbstract
{
    /**
     * A PCRE to match the hierarchical field path without delimiter.
     * @var string
     */
    protected $complexFieldFilter = 'lineItems/[^/]+';

    /**
     * For which class is this description used?
     * @var string
     */
    protected $modelClass = Cart::class;

    /**
     * Creates the update action for the given class and data.
     * @param mixed $changedValue
     * @param ClassMetadataInterface $metadata
     * @param array $changedData
     * @param array $oldData
     * @param Cart $sourceObject
     * @param string $subFieldName If you work on attributes etc. this is the name of the specific attribute.
     * @return AbstractAction[]
     */
    public function createUpdateActions(
        $changedValue,
        ClassMetadataInterface $metadata,
        array $changedData,
        array $oldData,
        $sourceObject,
        string $subFieldName = ''
    ): array {
        // Process only on new items
        if (!isset($changedValue['productId']) || !$changedValue['productId']) {
            return [];
        }

        $action = CartAddLineItemAction::fromArray([
            'productId' => $changedValue['productId'],
            'variantId' => $changedValue['variant']['id'],
            'quantity' => $changedValue['quantity']
        ]);

        if (isset($changedValue['distributionChannel']['id'])) {
            $action->setDistributionChannel(ChannelReference::ofId($changedValue['distributionChannel']['id']));
        }

        return [$action];
    }
}
