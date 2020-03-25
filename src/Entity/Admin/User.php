<?php


namespace App\Entity\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\UserRepository")
 */
class User
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=30, name="first_name")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30, name="last_name")
     */
    private $lastName;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $phoneNumber;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passwordResetToken;

    /**
     * @ORM\Column(type="date", length=255, nullable=true)
     */
    private $requestedPasswordAt;

    /**
     * @ORM\Column(type="date", length=255)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", length=1)
     */
    private $activated;

    /**
     * One user has many roles. This is the inverse side.
     * @ORM\OneToMany(targetEntity="UserRoles", mappedBy="user")
     */
    private $roles;

    /**
     * @ORM\ManyToOne(targetEntity="Perimeter")
     * @ORM\JoinColumn(name="perimeter_id", referencedColumnName="id")
     */
    private $perimeter;



}