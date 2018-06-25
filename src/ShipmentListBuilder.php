<?php

namespace Drupal\commerce_shipping;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the list builder for shipments.
 */
class ShipmentListBuilder extends EntityListBuilder {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new PaymentListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage,  RouteMatchInterface $route_match) {
    parent::__construct($entity_type, $storage);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $order = $this->routeMatch->getParameter('commerce_order');
    return $this->storage->loadMultipleByOrder($order);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    /** @var \Drupal\commerce_shipping\Entity\Shipment $entity */
    $operations = parent::getOperations($entity);

    if (isset($operations['edit'])) {
      $operations['edit'] = [
        'title' => $this->t('Edit'),
        'weight' => 30,
        'url' => $entity->toUrl('edit-form')->setRouteParameter('commerce_order', $entity->getOrderId()),
      ];
    }

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Shipment');
    $header['items'] = $this->t('Items');
    $header['tracking_code'] = $this->t('Tracking Pin');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\commerce_shipping\Entity\ShipmentInterface $entity */
    $items = [];

    foreach ($entity->getItems() as $item) {
      $items[] = $item->getTitle();
    }

    $row['label'] = $entity->getTitle();
    $row['items'] = implode(",\n", $items);
    $row['tracking_code'] = $entity->getTrackingCode() ? $entity->getTrackingCode() : 'N/A';

    return $row + parent::buildRow($entity);
  }

}
