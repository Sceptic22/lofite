<?php

namespace Lofite\LofiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;


class PageController extends Controller
{

    public function indexAction()
    {
        $em =  $this->getDoctrine()->getManager();
        $list_portfolio = $em->getRepository('Lofite\LofiteBundle\Entity\Portfolio')->findAll();
        $imgs=array();



        foreach ($list_portfolio as $portf)
        {
            $img=$em->getRepository('Lofite\LofiteBundle\Entity\Images')
                ->findOneBy(array('portfolioId'=>$portf->getId(),'ismainphoto'=>true));
            if($img)
            $imgs[]=$img->getPath();
            else
                $imgs[]="";
        }


        $contacts = $em->getRepository('Lofite\LofiteBundle\Entity\Contacts')->findAll();
        if($contacts)
            $contacts=$contacts[0];

        $vacancies=$em->getRepository('Lofite\LofiteBundle\Entity\Vacancies')->findAll();

        return $this->render('LofiteBaseBundle:Page:index.html.twig',
            array('list_portfolio' => $list_portfolio,'contacts'=>$contacts,'imgs'=>$imgs,'vacancies'=>$vacancies, 'portfolio'=>$portfolio,'imgs'=>$imgs,'mainPhoto'=>$mainPhoto));


    }

    public function reviewAction(Request $request)
    {
        $id=$request->query->get('id');

        if(empty($id) || !is_numeric($id) )
        {
            return $this->redirect($this->generateUrl('LofiteBaseBundle_homepage'));
        }

        $em =  $this->getDoctrine()->getManager();
        $portfolio=$em->getRepository('Lofite\LofiteBundle\Entity\Portfolio')->findOneBy(array('id'=>$id));

        if(empty($portfolio))
            return $this->redirect($this->generateUrl('LofiteBaseBundle_homepage'));

        //
        $repository = $em->getRepository('Lofite\LofiteBundle\Entity\Images');
        $query = $repository->createQueryBuilder('i')
            ->where('i.portfolioId = :id')
            ->setParameter('id', $portfolio->getId())
            ->getQuery();
        $imgs= $query->getResult();
        //

        $mainPhoto="";

        for ($i=0;$i<count($imgs);$i++)
        {
            if ($imgs[$i]->getIsmainphoto() == true)
            {
            $mainPhoto = $imgs[$i]->getPath();
                break;
                 }
        }



        return $this->render('LofiteBaseBundle:Page:review.html.twig',
            array('portfolio'=>$portfolio,'imgs'=>$imgs,'mainPhoto'=>$mainPhoto));
    }

    public function sendMailAction(Request $request)
    {
        if(empty($request->request->get('email'))
            || empty($request->request->get('name'))
            || empty($request->request->get('message')))
            return $this->redirect($this->generateUrl('LofiteBaseBundle_homepage'));

        $constraint = new Collection(array(
            'email' => new Email(),


            'name' =>new Length(array('min'=> 4, 'max'=> 20,
                'minMessage' => 'Your  name must be at least {{ limit }} characters long',
                'maxMessage' => 'Your  name cannot be longer than {{ limit }} characters')),


            'message' =>new Length(array('min'=> 20, 'max'=> 500,
                'minMessage' => 'Your  message must be at least {{ limit }} characters long',
                'maxMessage' => 'Your  message cannot be longer than {{ limit }} characters'))
        ));

        $violationList = $this->get('validator')->validateValue($request->request->all(), $constraint);

        if(count($violationList)==0)
        {
            $em =  $this->getDoctrine()->getManager();
            $contacts = $em->getRepository('Lofite\LofiteBundle\Entity\Contacts')->findAll();
            if($contacts)
            {

                $contacts = $contacts[0];

               $this->sendMail('New request from Lofite',
                    $request->request->get('email').'<br/>'.$request->request->get('message'),
                    $contacts->getEmail());

            }

        }

        return $this->render('LofiteBaseBundle:Page:errorsMail.html.twig',
            array('violationList'=>$violationList));

    }

    private function sendMail($subject, $template, $to)
    {
        //Estas lineas son para cuando el servidor tiene un certificado no valido
        $https['ssl']['verify_peer'] = FALSE;
        $https['ssl']['verify_peer_name'] = FALSE;

        $transport = \Swift_SmtpTransport::newInstance($this->container->getParameter('mailer_host'), $this->container->getParameter('mailer_port'),  $this->container->getParameter('mailer_encryption'))
            ->setUsername($this->container->getParameter('mailer_user'))
            ->setPassword($this->container->getParameter('mailer_password'))
            ->setStreamOptions($https)
        ;

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($to)
            ->setBody($template,'text/html');

        return $this->get('mailer')->newInstance($transport)->send($message);
    }

}

?>