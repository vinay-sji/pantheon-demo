<?php

namespace Drupal\weshare\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Views;

/**
 * Provides a Need List Block.
 *
 * @Block(
 *   id = "need_list_block",
 *   admin_label = @Translation("Need List block"),
 *   category = @Translation("Weshare"),
 * )
 */
class NeedListBlock extends BlockBase implements BlockPluginInterface {

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
      $q->condition('type', 'needs')
      ->condition('status', true);



      $nids = $q->execute();

      $needs = [];
      if (!empty($nids)) {
        $all_needs = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
        foreach ($all_needs as $need) {
            $agencies = [];
            foreach ($need->field_agencies_list->referencedEntities() as $term) {
                $agencies[] = $term->id();
            }

            if (!empty($agencies)) {
                if (in_array($account->field_agency_company->entity->id(), $agencies)) {
                    $needs[] = $need->id();
                    continue;
                }
            }
            else {
                $needs[] = $need->id();

            }

        }
      }

        // rendering need view
        $view = Views::getView('need_list');
        $view->setDisplay('block_2');
        // contextual relationship filter
        $needs=implode('+',$needs);
        $view->setArguments([$needs]);
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