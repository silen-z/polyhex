<?php declare(strict_types=1);

namespace SilenZ\App\Database\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity]
final class User
{
    public function __construct(
        #[Column(type: "primary")]
        public int    $id,

        #[Column(type: "string")]
        public string $name,
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}