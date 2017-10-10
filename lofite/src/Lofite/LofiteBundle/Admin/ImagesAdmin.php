<?php
namespace Lofite\LofiteBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class ImagesAdmin extends Admin
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
            ->add('path', null, array('label' => 'Картинка'))
            ->add('ismainphoto', null, array('label' => 'Основная'))
            ->add('portfolio', 'sonata_type_model', array('required'=>true,'label' => 'Родительское портфолио',
                'class' => 'Lofite\LofiteBundle\Entity\Portfolio',
                'property' => 'name'));


    }

    /**
     * Конфигурация формы редактирования записи
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        if($this->hasParentFieldDescription())
        {
            $getter = 'get' . $this->getParentFieldDescription()->getFieldName();

            $parent = $this->getParentFieldDescription()->getAdmin()->getSubject();
            if ($parent)
            {
                $currentCount=$this->getParentFieldDescription()->getAdmin()->getCount();
                $image = $parent->$getter()[$currentCount];

            } else
                {
                $image = null;
            }
        } else
            {
            $image = $this->getSubject();
        }

        $data="";$fullPath="";

        if ($image &&  $image->getPath())
        {

            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath().'/'.$image->getPath();

            $data = '<img src="'.$fullPath.'" style="max-height: 256px; max-width: 256px;border:1px solid black;margin:5px;" title="текущее изображение" />';
        }

        $formMapper
            ->add('file', 'file', array(
                'attr' =>array('data-img' =>$fullPath )
            ,'help'=>$data,'required' => false,'label' => 'Загрузить фото'))
            ->add('ismainphoto', null, array('label' => 'Основная'))
            ->add('portfolio', 'sonata_type_model', array('required'=>true,'label' => 'Родительское портфолио',
                'class' => 'Lofite\LofiteBundle\Entity\Portfolio',
                'property' => 'name'));
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
            ->add('path', null, array('label' => 'Картинка','template' => 'LofiteBaseBundle:Admin:picture.html.twig'))
            ->add('ismainphoto', null, array('label' => 'Основная'))
            ->add('portfolio', 'sonata_type_model', array('required'=>true,'label' => 'Родительское портфолио',
                'class' => 'Lofite\LofiteBundle\Entity\Portfolio',
                'property' => 'name'));
    }

    /**
     * Поля, по которым производится поиск в списке записей
     *
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        /*$datagridMapper
            ->add('telephone', null, array('label' => 'Телефон'))
            ->add('email', null, array('label' => 'Email'));*/
    }


    //

    public function prePersist($img) {
        $this->saveFile($img);
    }

    public function preUpdate($img) {
        $this->saveFile($img);
    }

    public function saveFile($img)
    {
        $basepath = $this->getRequest()->getBasePath();
        $img->upload($basepath);
    }


}

?>