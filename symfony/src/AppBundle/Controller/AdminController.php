<?php
namespace AppBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class AdminController extends BaseAdminController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUserAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);
        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $fields = $this->entity['show']['fields'];

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            unset($fields['created']);
        }

        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        return $this->render($this->entity['templates']['show'], array(
            'entity' => $entity,
            'fields' => $fields,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * when edit user action
     *
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function editUserAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_EDIT);
        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new \RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            return new Response((string)$newValue);
        }

        $fields = $this->entity['edit']['fields'];

        $editForm = $this->createEditForm($entity, $fields);
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $editForm->remove('enabled');
            $editForm->remove('roles');
            $editForm->remove('locked');
        }

        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        if ($editForm->isValid()) {
            $this->preUpdateUserEntity($entity);
            $this->em->flush();

            $refererUrl = $this->request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirect($this->generateUrl('easyadmin', array('action' => 'show', 'entity' => $this->entity['name'], 'id' => $id)));
        }

        return $this->render($this->entity['templates']['edit'], array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function updateEntityProperty($entity, $property, $value)
    {
        $entityConfig = $this->entity;

        // the method_exists() check is needed because Symfony 2.3 doesn't have isWritable() method
        if (method_exists($this->get('property_accessor'), 'isWritable') && !$this->get('property_accessor')->isWritable($entity, $property)) {
            throw new \RuntimeException(sprintf('The "%s" property of the "%s" entity is not writable.', $property, $entityConfig['name']));
        }

        $this->dispatch(EasyAdminEvents::PRE_UPDATE, array('entity' => $entity, 'newValue' => $value));

        $this->get('property_accessor')->setValue($entity, $property, $value);

        $this->em->persist($entity);
        $this->em->flush();
        $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity, 'newValue' => $value));

        $this->dispatch(EasyAdminEvents::POST_EDIT);
    }

    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function prePersistUserEntity($user)
    {
         $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    public function preUpdateUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }
}