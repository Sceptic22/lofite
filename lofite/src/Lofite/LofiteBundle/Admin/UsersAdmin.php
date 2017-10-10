<?php
namespace Lofite\LofiteBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Knp\Menu\ItemInterface as MenuItemInterface;

class UsersAdmin extends Admin
{
    /**
     * Конфигурация отображения записи
     *
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id', null, array('label' => 'Идентификатор'))
            ->add('username', null, array('label' => 'Имя пользователя'));
    }

    /**
     * Конфигурация формы редактирования записи
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username', null, array('label' => 'Имя пользователя'))
            ->add('password', null, array('label' => 'Пароль'));
    }

    /**
     * Конфигурация списка записей
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, array('label' => 'Идентификатор'))
            ->add('username', null, array('label' => 'Имя пользователя'));
    }


    //

    public function preUpdate($user)
    {
       $this->encodePassword($user);
    }

    public function prePersist($user)
    {
        $entity = new \Lofite\LofiteBundle\Entity\Roles();
        $roles = $this->modelManager
            ->getEntityManager($entity)->getRepository('Lofite\LofiteBundle\Entity\Roles');

        $role=$roles->findOneBy(array('name'=>"ROLE_ADMIN"));

        if($role)
        {
            $user->getUserRoles()->add($role);
        }

        $this->encodePassword($user);
    }

    private function encodePassword($user)
    {
        $user->encodePassword();
    }




}

?>