<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Index(columns: ['permalink'], name: 'permalink_idx')]
class Category
{
    #[ORM\Id]
    #[ORM\Column(name: '_id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column(name: 'name', length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(name: 'url_rewrite', length: 255, nullable: false)]
    private ?string $urlRewrite = null;

    #[ORM\Column(name: 'permalink', length: 255, unique: true)]
    private ?string $permalink = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Category::class, fetch: 'EAGER')]
    private Collection $children;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'id_parent', referencedColumnName: '_id')]
    private ?Category $parent = null;

    #[ORM\ManyToMany(targetEntity: Page::class, mappedBy: 'categories')]
    private Collection $pages;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $uuid): self
    {
        $this->id = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrlRewrite(): ?string
    {
        return $this->urlRewrite;
    }

    public function setUrlRewrite(string $urlRewrite): self
    {
        $this->urlRewrite = $urlRewrite;

        return $this;
    }

    public function getPermalink(): ?string
    {
        return $this->permalink;
    }

    public function setPermalink(string $permalink): self
    {
        $this->permalink = $permalink;

        return $this;
    }

    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
            $page->addCategory($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            $page->removeCategory($this);
        }

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChildren(Category $category): self
    {
        if (!$this->children->contains($category)) {
            $this->children->add($category);
        }

        return $this;
    }

    public function removeChildren(Category $category): self
    {
        $this->children->removeElement($category);

        return $this;
    }

    public function getParent(): ?Category
    {
        return $this->parent ?? null;
    }

    public function setParent(?Category $category): self
    {
        $this->parent = $category;
        return $this;
    }

}
