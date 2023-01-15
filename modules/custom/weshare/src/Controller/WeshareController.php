<?php

namespace Drupal\weshare\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class WeshareController extends ControllerBase {

    public function fulfillNeedHandler(Request $r) {
        $nid = $r->request->get('nid');
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
        if ($node->field_vibility->value = 'Active') {
            $node->field_vibility->value = 'Pending';
            $node->save();
            weshare_send_pending_notification($node, "needs");
            $response = new JsonResponse([
                'success' => TRUE,
                'data' => [
                    'nid' => $nid,
                    'msg' => 'node updated',
                ],
            ]);
        }
        else {
            $response = new JsonResponse([
                'success' => FALSE,
                'error' => 'Could not load: ' . $nid,
            ]);
        }

        return $response;
    }

    public function fulfillSupportHandler(Request $r) {
        $nid = $r->request->get('nid');
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
        if ($node->field_status->value = 'Active') {
            $node->field_status->value = 'Pending';
            $node->save();
            weshare_send_pending_notification($node, "services");
            $response = new JsonResponse([
                'success' => TRUE,
                'data' => [
                    'nid' => $nid,
                    'msg' => 'node updated',
                ],
            ]);
        }
        else {
            $response = new JsonResponse([
                'success' => FALSE,
                'error' => 'Could not load: ' . $nid,
            ]);
        }
        return $response;
    }

    public function approveNeedHandler($nid) {
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
        if ($node->field_approved->value == 0) {
            $node->field_approved->value = 1;
            $node->save();
        }
        $title = $node->title->value;
        $title = str_replace(" ", "-", $title);
        $path = '/need/'.$title;
    
        $response = new \Symfony\Component\HttpFoundation\RedirectResponse($path);
        $response->send();
        \Drupal::messenger()->addStatus(t('The need has been approved.'));
        return;
    }

    public function approveServiceHandler($nid) {
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
        if ($node->field_approved_service->value == 0) {
            $node->field_approved_service->value = 1;
            $node->save();
        }
        $title = $node->title->value;
        $title = str_replace(" ", "-", $title);
        $path = '/service/'.$title;
    
        $response = new \Symfony\Component\HttpFoundation\RedirectResponse($path);
        $response->send();
        \Drupal::messenger()->addStatus(t('The service has been approved.'));
        return;
    }

    public function services() {
        return [];
    }
    
/**
 * Fulfilled Need status
 */
    public function fulfilledNeedHandler(Request $r) {
        $nid = $r->request->get('nid');
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
        if ($node->field_vibility->value = 'Pending') {
            $node->field_vibility->value = 'Fullfilled';
            $node->save();
            $response = new JsonResponse([
                'success' => TRUE,
                'data' => [
                    'nid' => $nid,
                    'msg' => 'need fulfilled',
                ],
            ]);
        }
        else {
            $response = new JsonResponse([
                'success' => FALSE,
                'error' => 'Could not load: ' . $nid,
            ]);
        }

        return $response;
    }
// Fullfill support status
    public function fulfilledSupportHandler(Request $r) {
        $nid = $r->request->get('nid');
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
        if ($node->field_status->value = 'Pending') {
            $node->field_status->value = 'Fullfilled';
            $node->save();
            $response = new JsonResponse([
                'success' => TRUE,
                'data' => [
                    'nid' => $nid,
                    'msg' => 'Support fulfilled',
                ],
            ]);
        }
        else {
            $response = new JsonResponse([
                'success' => FALSE,
                'error' => 'Could not load: ' . $nid,
            ]);
        }

        return $response;
    }


    public function approveDataHandler($nid) {
        $validationResult = weshare_validate_approve_reject($nid);
        $response = is_array($validationResult) ? $validationResult[0] : $validationResult;

        if($response == "valid") {
            $node = $validationResult[1] ? $validationResult[1] : "";
            
            if ($node && $node->bundle() == "needs") {
                // approve need
                $node->field_approved->value = 1;
                $node->field_rejected->value = 0;
                $node->save();
                return new RedirectResponse(Url::fromUri('internal:/pending-needs')->toString());
            }

            if ($node && $node->bundle() == "services") {
                $node->field_approved_service->value = 1;
                $node->field_rejected->value = 0;
                $node->save();
                return new RedirectResponse(Url::fromUri('internal:/pending-services')->toString());
            }
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }elseif ($response == "unauthorised-user") {
            \Drupal::messenger()->addWarning(t('You do not have the approval access for this Need/Support!'));
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }elseif ($response == "invalid-node") {
            \Drupal::messenger()->addWarning(t('Sorry, the need/support does not exist or has been deleted!'));
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }elseif ($response == "invalid-agency" || $response == "unauthorised-access") {
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }else {
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }
    }

    public function rejectDataHandler($nid) {
        $validationResult = weshare_validate_approve_reject($nid);
        $response = is_array($validationResult) ? $validationResult[0] : $validationResult;

        if($response == "valid") {
            // approve
            $node = $validationResult[1] ? $validationResult[1] : "";
            if ($node && $node->bundle() == "needs") {
                $node->field_rejected->value = 1;
                $node->field_approved->value = 0;
                $node->save();
                return new RedirectResponse(Url::fromUri('internal:/pending-needs')->toString());
            }

            if ($node && $node->bundle() == "services") {
                $node->field_rejected->value = 1;
                $node->field_approved_service->value = 0;
                $node->save();
                return new RedirectResponse(Url::fromUri('internal:/pending-services')->toString());
            }
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }elseif ($response == "unauthorised-user") {
            \Drupal::messenger()->addWarning(t('You do not have the approval access for this Need/Support!'));
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }elseif ($response == "invalid-node") {
            \Drupal::messenger()->addWarning(t('Sorry, the need/support does not exist or has been deleted!'));
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }elseif ($response == "invalid-agency" || $response == "unauthorised-access") {
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }else {
            return new RedirectResponse(Url::fromRoute('system.403')->toString());
        }
    }

}