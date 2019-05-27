<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;

use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"title"},message="Une autre annonce possede déjà le meme titre")
 * @Vich\Uploadable()
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10,max=255,minMessage="Le titre doit faire plus de 10 caracteres",maxMessage="Titre trop logng : pas plus de 255 caracteres")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=30,minMessage="Le résumé doit faire plus de 30 caracteres")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=100,minMessage="Le descriptif doit faire plus de 100 caracteres")
     */
    private $content;

    /**
     * @Vich\UploadableField(mapping="ads_image",fileNameProperty="coverImage")
     * @Assert\Image(mimeTypes="image/jpeg")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string|null
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad",orphanRemoval=true,cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="ad")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="datetime",)
     *
     */
    private $updated_at;


    /**
     * Création automatique d'une date pour l'image CoverImage
     * @ORM\PrePersist
     *
     * @return void
     */
    public function prePersist(){

        if(empty($this->updated_at)){
            $this->created_at = new \DateTime();
        }
    }


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Création d'une fonction pour initialiser le slug (avant le persist et avant la Maj)
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */

    public function initializeSlug(){
        if(empty($this->slug)){

           $slugify = new Slugify();
           $this->slug = $slugify->slugify($this->title);
        }
    }

    /**
     * Permet de recuperer le commentaire d'un auteur par rapport a un annonce
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author){

        foreach ($this->comments as $comment){

            if($comment->getAuthor() === $author) return $comment;
        }
        return null;
    }

    public function getAverageRatings(){

        // calcul de la somme des notes
        $sum = array_reduce($this->comments->toArray(),function($total,$comment){

            // on retourne le total plus la note de chaque commentaire

            return $total + $comment->getRating();

        },0);

        // on divise le total par le nombre de notes

        if (count($this->comments)>0) return $sum / count($this->comments);
        return 0;
    }

    public function getNotAvailableDays(){

        $notAvailableDays = [];

        foreach($this->bookings as $booking){

            $resultat = range($booking->getStartDate()->getTimestamp(),$booking->getEndDate()->getTimestamp(),24 * 60 * 60);
            $days = array_map(function($dayTimestamp){

                return new \DateTime(date('Y-m-d',$dayTimestamp));
            },$resultat);

            $notAvailableDays = array_merge($notAvailableDays,$days);

        }
        return $notAvailableDays;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    /**
     * @param null|string $coverImage
     * @return Ad
     */
    public function setCoverImage(?string $coverImage): Ad
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?file
    {
        return $this->imageFile;
    }

    /**
     * @param File $imageFile
     * @return Ad
     * @throws \Exception
     */
    public function setImageFile(file $imageFile): Ad
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile){
            $this->updated_at = new \Datetime('now');
        }

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

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
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
