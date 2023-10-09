<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'page')]
#[ORM\Index(columns: ['status'], name: 'status_idx')]
#[ORM\Index(columns: ['id_page', 'status'], name: 'id_page_status_idx')]
#[ORM\Index(columns: ['id_main_category'], name: 'id_main_category_idx')]
#[ORM\Index(columns: ['id_user'], name: 'id_user_idx')]
class Page
{
    #[ORM\Id]
    #[ORM\Column(name: '_id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column(name: 'id_page', type: 'integer', columnDefinition:'INT AUTO_INCREMENT')]
    private ?int $idPage = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resume;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content;

    #[ORM\Column(name: 'date_update', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateUpdate;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'default' => 1])]
    private int $status = 1;

    #[ORM\Column(name: 'tracking_view', type: 'integer', options: ['default' => 0])]
    private int $trackingView = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: '_id')]
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'id_main_category', referencedColumnName: '_id')]
    private ?Category $mainCategory;

    #[ORM\Column(name: 'url_rewrite', type: "string", length: 255, unique: true, nullable: false)]
    private string $urlRewrite;

    #[ORM\Column(name: 'permalink', type: 'string', length: 255, unique: true, nullable: true)]
    private ?string $permalink;

    private ?array $orderedCategories = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'pages')]
    #[ORM\JoinColumn(name: 'id_page', referencedColumnName: '_id')]
    #[ORM\InverseJoinColumn(name: 'id_category', referencedColumnName: '_id')]
    #[Groups(['read_single'])]
    private Collection $categories;

    private ?string $fullUrl = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

    public function getIdPage(): ?int
    {
        return $this->idPage;
    }

    public function setIdPage(int $idPage): self
    {
        $this->idPage = $idPage;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): self
    {
        $this->resume = $resume;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(?\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getTrackingView(): int
    {
        return $this->trackingView;
    }

    public function setTrackingView(int $trackingView): self
    {
        $this->trackingView = $trackingView;
        return $this;
    }

    public function getMainCategory(): ?Category
    {
        return $this->mainCategory;
    }

    public function setMainCategory(?Category $mainCategory): self
    {
        $this->mainCategory = $mainCategory;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUrlRewrite(): string
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

    public function setPermalink(?string $permalink): self
    {
        $this->permalink = $permalink;
        return $this;
    }


    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getFullUrl(): string
    {
        return sprintf('%s-%d.html', $this->getPermalink(), $this->getIdPage());
    }

    public function setFullUrl(): self
    {
        return $this;
    }

    /**
     * Get the ordered categories from the lowest to the root.
     *
     * @return string|null The ordered categories as a string, or null if no categories are found.
     */
    public function getOrderedCategories(): ?string
    {
        // Retrieve the categories associated with the page
        $categories = $this->getCategories();

        // Initialize an empty array to store the ordered categories
        $orderedCategories = [];

        // Loop through each category to build its full path from the root
        foreach ($categories as $category) {

            // Start with the current category
            $current = $category;

            // Initialize an empty array to store the individual parts of the path
            $path = [];

            // Build the path from the current category up to the root
            while ($current !== null) {

                // Add the current category's name to the beginning of the path array
                array_unshift($path, $current->getName());

                // Move up to the parent category for the next iteration
                $current = $current->getParent();
            }

            // Convert the path array to a string and add it to the ordered categories array
            $orderedCategories[] = implode(' > ', $path);
        }

        // Return the last element in the ordered categories array, which is the full path from the lowest category to the root
        return end($orderedCategories) ?: null;
    }

    public function setOrderedCategories(): self
    {
        return $this;
    }

}

