<?php


namespace Bx\Model\Models;

use Bx\Model\AbsOptimizedModel;
use Bitrix\Main\Type\DateTime;

class File extends AbsOptimizedModel
{
    protected function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'src' => $this->getSrc(),
            'type' => $this->getType(),
            'date_create' => $this->getDateCreate(),
            'size' => $this->getSize(),
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
            'description' => $this->getDescription(),
        ];
    }

    public function getId(): int
    {
        return (int)$this['ID'];
    }

    public function getSrc(): string
    {
        return (string)\CFile::GetFileSRC($this->data);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return (string)$this['CONTENT_TYPE'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this['ORIGINAL_NAME'];
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return (int)$this['FILE_SIZE'];
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return (int)$this['HEIGHT'];
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return (int)$this['WIDTH'];
    }

    /**
     * @return DateTime|null
     */
    public function getDateCreate(): ?DateTime
    {
        return $this['TIMESTAMP_X'] instanceof DateTime ? $this['TIMESTAMP_X'] : null;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this['DESCRIPTION'];
    }
}
