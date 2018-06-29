<?php

namespace Drupal\commerce_shipping;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for shipments.
 *
 * @see \Drupal\commerce_shipping\Entity\Shipment
 */
class ShipmentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\commerce_shipping\Entity\ShipmentInterface $entity */
    $order = $entity->getOrder();
    $access = AccessResult::allowedIfHasPermission($account, $this->entityType->getAdminPermission())
      ->andIf(AccessResult::allowedIf($order && $order->access('view', $account, TRUE)))
      ->addCacheableDependency($entity);

    return $access;
  }

}
