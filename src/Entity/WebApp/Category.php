<?php

namespace App\Entity\WebApp;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebApp\Category\Doctrine\CategoryRepository")
 * @ORM\Table(name="categories")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"default"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private $city;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=4)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=4)
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WebApp\Season", inversedBy="categories")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"category"})
     */
    private $season;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WebApp\User", inversedBy="categories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WebApp\Comment", mappedBy="category", cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WebApp\Photo", mappedBy="category", orphanRemoval=true)
     * @Groups({"category"})
     */
    private $photos;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\WebApp\Tag", inversedBy="categories", orphanRemoval=true)
     * @ORM\JoinTable(name="categories_tags")
     * @Groups({"category"})
     */
    private $tags;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"default"})
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"default"})
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     * @Groups({"default"})
     */
    private $metaDescription;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

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

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setCategory($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getCategory() === $this) {
                $comment->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setCategory($this);
        }

        return $this;
    }

    public function resetPhoto(): self
    {
        $this->photos->clear();

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getCategory() === $this) {
                $photo->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function resetTags(): self
    {
        $this->tags->clear();

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}
