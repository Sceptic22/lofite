<?php

namespace Lofite\LofiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Finder\Finder;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Images
 *
 * @ORM\Table(name="images", indexes={@ORM\Index(name="portfolioId", columns={"portfolioId"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Images
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isMainPhoto", type="boolean", nullable=false)
     */
    private $ismainphoto;

    /**
     * @var \Lofite\LofiteBundle\Entity\Portfolio
     *
     * @ORM\ManyToOne(targetEntity="Lofite\LofiteBundle\Entity\Portfolio",inversedBy="images")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portfolioId", referencedColumnName="id")
     * })
     */
    private $portfolio;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Images
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set ismainphoto
     *
     * @param boolean $ismainphoto
     * @return Images
     */
    public function setIsmainphoto($ismainphoto)
    {
        $this->ismainphoto = $ismainphoto;

        return $this;
    }

    /**
     * Get ismainphoto
     *
     * @return boolean 
     */
    public function getIsmainphoto()
    {
        return $this->ismainphoto;
    }

    /**
     * Set portfolio
     *
     * @param \Lofite\LofiteBundle\Entity\Portfolio $portfolio
     * @return Images
     */
    public function setPortfolio(\Lofite\LofiteBundle\Entity\Portfolio $portfolio = null)
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    /**
     * Get portfolio
     *
     * @return \Lofite\LofiteBundle\Entity\Portfolio 
     */
    public function getPortfolio()
    {
        return $this->portfolio;
    }


    /**
     * @var integer
     *
     * @ORM\Column(name="portfolioId", type="integer", nullable=false)
     */
    private $portfolioId;

    public function getPortfolioId()
    {
        return $this->portfolioId;
    }

    public function setPortfolioId($portfolioId)
    {
        $this->portfolioId=$portfolioId;
    }

    //

    protected $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file=$file;
    }



    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir($basepath)
    {
        // the absolute directory path where uploaded documents should be saved
        return $basepath.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'photo';
    }

    public function upload($basepath)
    {

        if (null === $this->file) {
            return;
        }

        if (null === $basepath)
        {
            return;
        }
        $time = date('d/m/Y H:i:s');
        $newname=md5($time);

        $ext = ".".pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);

        $this->file->move($this->getUploadRootDir($basepath), $newname.$ext);
        $this->setPath($this->getUploadRootDir($basepath)."/".$newname.$ext);

        $this->file = null;
    }


    /**
     * @ORM\PreRemove()
     */
    public function mRemove()
    {
        $path=$this->getPath();
        $this->removeImage($path);
    }


    /**
     * @ORM\PreUpdate()
     */
    public function mUpdate($args)
    {
        $changeSet = $args->getManager()->getUnitOfWork()->getEntityChangeSet(
            $args->getEntity()
        );

        //if photo changed
        if(array_key_exists('path', $changeSet))
        {
            //get old path
            $oldpath=$changeSet['path'][0];

            //if old path != new path -> remove old photo
            if(strcmp($oldpath,$this->getPath())!=0)
                $this->removeImage($oldpath);
        }

    }


    private function removeImage($path)
    {
        $fs = new Filesystem();

        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../../../../../public_html/photo');

        $indx=strripos($path,"/")+1;
        $name=substr($path,$indx);

        foreach ($finder as $file)
        {
            $filename= $file->getRelativePathname();

                if(strcmp($filename,$name)==0)
                {
                     try
                     {
                          $fs->remove($file->getRealPath());
                     } catch (IOExceptionInterface $e) {
                         echo "file {".$filename."} not remove";
                     }
                break;

                 }


        }
    }



}
