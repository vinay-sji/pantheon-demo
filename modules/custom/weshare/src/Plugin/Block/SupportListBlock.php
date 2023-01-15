<?php

namespace Drupal\weshare\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Views;

/**
 * Provides a Support List Block.
 *
 * @Block(
 *   id = "support_list_block",
 *   admin_label = @Translation("Support List block"),
 *   category = @Translation("Weshare"),
 * )
 */
class SupportListBlock extends BlockBase implements BlockPluginInterface {

    public function getCacheMaxAge() {
        return 0;
    }

    public function access(AccountInterface $account, $return_as_object = FALSE) {
        return AccessResult::allowedIf($account->isAuthenticated());
    }

  /**
   * {@inheritdoc}
   */
  public function build() {
      $current_user = \Drupal::currentUser();
      $account = \Drupal::entityTypeManager()->getStorage('user')->load($current_user->id());

      $q = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
      $q->condition('type', 'services')
      ->condition('status', true);

      /*$or = $q->orConditionGroup()
      ->condition('field_service_agencies', NULL, 'IS NULL')
      ->condition('field_service_agencies.entity:taxonomy_term.tid', $account->field_agency_company->entity->id())
      ;

      $q->condition($or);*/

      $nids = $q->execute();

      $services = [];
      if (!empty($nids)) {
        $all_services = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
        foreach ($all_services as $service) {
            $agencies = [];
            foreach ($service->field_service_agencies->referencedEntities() as $term) {
                $agencies[] = $term->id();
            }

            if (!empty($agencies)) {
                if (in_array($account->field_agency_company->entity->id(), $agencies)) {
                    $services[] = $service->id();
                    continue;
                }
            }
            else {
                $services[] = $service->id();

            }

            
            if ($service ->field_protected->value == 1) {
               $protected_user_ids = [];
               foreach ($service->field_protection_settings_servi->referencedEntities() as $puser) {
                $protected_user_ids[] = $puser->id();
               }

               if (in_array($current_user->id(), $protected_user_ids)) {
                $services[] = $service->id();
                continue;
               }
            }

        }
      }

      //var_dump($services);die;


        // rendering support view
        $view = Views::getView('service');
        $view->setDisplay('block_1');
        // contextual relationship filter
        $services=implode('+',$services);
        $view->setArguments([$services]);
        $view->execute();
        $build = $view->render();
        $renderer = \Drupal::service('renderer');
        $output = $renderer->render($build);

        return [
            '#markup' => $output,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
  }

}