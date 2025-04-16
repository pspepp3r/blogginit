<?php

// declare(strict_types=1);

// namespace Src\Entities;

// use Doctrine\ORM\Mapping\Id;
// use Doctrine\ORM\Mapping\Table;
// use Doctrine\ORM\Mapping\Column;
// use Doctrine\ORM\Mapping\Entity;
// use Doctrine\ORM\Mapping\OneToMany;
// use Doctrine\ORM\Mapping\GeneratedValue;
// use Doctrine\Common\Collections\Collection;
// use Doctrine\Common\Collections\ArrayCollection;

// #[Entity, Table('categories')]
// class Category
// {
//     #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
//     private int $id;

//     #[Column]
//     private string $name;

//     #[OneToMany(Blog::class, 'category', ['remove'])]
//     private Collection $blogs;

//     public function __construct()
//     {
//         $this->blogs = new ArrayCollection();
//     }

//     public function getId(): int
//     {
//         return $this->id;
//     }

//     public function getName(): string
//     {
//         return $this->name;
//     }

//     public function setName(string $name): static
//     {
//         $this->name = $name;

//         return $this;
//     }

//     public function getBlogs(): Collection
//     {
//         return $this->blogs;
//     }

//     public function addBlog(Blog $blogs): static
//     {
//         $this->blogs->add($blogs);

//         return $this;
//     }
// }
