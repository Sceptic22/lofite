<?php

namespace Lofite\LogiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Lofite\LofiteBundle\Entity\Users;
use  Lofite\LofiteBundle\Entity\Roles;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class FixtureLoader implements FixtureInterface
{
    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        // создание роли ROLE_ADMIN
        $role = new Roles();
        $role->setName('ROLE_ADMIN');

        $manager->persist($role);

        // создание пользователя
        $user = new Users();
        $user->setUsername('lofite_admin');
        $user->setSalt(md5(time()));

        // шифрует и устанавливает пароль для пользователя,
        // эти настройки совпадают с конфигурационными файлами
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword('Gb$5^jgt9*_M', $user->getSalt());
        $user->setPassword($password);

        $user->getUserRoles()->add($role);

        $manager->persist($user);

        $manager->flush();

    }
}

?>