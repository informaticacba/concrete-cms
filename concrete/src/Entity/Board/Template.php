<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Board\Template\Driver\DriverInterface;
use Concrete\Core\Board\Template\Driver\Manager;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Support\Facade\Facade;
use HtmlObject\Image;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="BoardTemplates"
 * )
 */
class Template implements \JsonSerializable
{
    use PackageTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $icon = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $name = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $handle = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @return $this
     */
    public function setHandle(string $handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return $this
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return tc('BoardTemplateName', $this->getName());
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param bool $asTag
     *
     * @return \HtmlObject\Image|string|null
     */
    public function getTemplateIconImage(bool $asTag = true)
    {
        if ($this->getIcon()) {
            $image = ASSETS_URL_IMAGES . '/icons/board_templates/' . $this->getIcon();
            if ($asTag) {
                $image = new Image($image);
            }
            return $image;
        }
    }

    public function getDriver() : DriverInterface
    {
        $app = Facade::getFacadeApplication();
        $manager = $app->make(Manager::class);
        return $manager->driver($this->getHandle());
    }

    public function export(\SimpleXMLElement $node): void
    {
        $template = $node->addChild('template');
        $template->addAttribute('handle', $this->getHandle());
        $template->addAttribute('name', h($this->getName()));
        $template->addAttribute('icon', h($this->getIcon()));
        $template->addAttribute('package', $this->getPackageHandle());
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'id' => $this->getId(),
            'icon' => $this->getIcon(),
            'slots' => $this->getDriver()->getTotalSlots(),
        ];
    }
}
