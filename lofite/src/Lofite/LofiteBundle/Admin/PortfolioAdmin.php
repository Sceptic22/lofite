<?php
namespace Lofite\LofiteBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Knp\Menu\ItemInterface as MenuItemInterface;

class PortfolioAdmin extends Admin
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
            ->add('name', null, array('label' => 'Название'))
            ->add('description', null, array('label' => 'Описание'))
            ->add('technologies', null, array('label' => 'Использованные технологии'))
            ->add('link', null, array('label' => 'Ссылка'));
    }

    /**
     * Конфигурация формы редактирования записи
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Portfolio')
            ->add('name', null, array('label' => 'Название'))
            ->add('description', null, array('label' => 'Описание'))
            ->add('technologies', null, array('label' => 'Использованные технологии'))
            ->add('link', null, array('label' => 'Ссылка'))
            ->end()
            ->with('Images')
            ->add('images', 'sonata_type_collection', array(
                'by_reference' => false
            ), array(
               'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ))
            ->end();

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
            ->add('name', null, array('label' => 'Название'))
            ->add('description', null, array('label' => 'Описание'))
            ->add('technologies', null, array('label' => 'Использованные технологии'))
            ->add('link', null, array('label' => 'Ссылка'));
    }

    /**
     * Поля, по которым производится поиск в списке записей
     *
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Название'))
            ->add('description', null, array('label' => 'Описание'))
            ->add('technologies', null, array('label' => 'Использованные технологии'))
            ->add('link', null, array('label' => 'Ссылка'));
    }


    public function prePersist($portfolio)
    {
        $this->preUpdate($portfolio);
        $this->saveFiles($portfolio);
    }

    public function preUpdate($portfolio)
    {
        $portfolio->setImages($portfolio->getImages());
        $this->saveFiles($portfolio);
    }

    private function saveFiles($portfolio)
    {
        $basepath = $this->getRequest()->getBasePath();
        foreach ($portfolio->getImages() as $img)
        $img->upload($basepath);
    }

    private $count=0;

    public function getCount()
    {
        return $this->count++;
    }


    public function configure()
    {
        $this->setTemplate('edit', 'LofiteBaseBundle:Admin:edit_javascript.html.twig');
    }


}

?>