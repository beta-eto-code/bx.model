<?php

namespace Bx\Model\UI\Fields;

use Bitrix\Main\UI\Filter\Options;

abstract class BaseFilterField
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $originalId;
    /**
     * @var bool
     */
    protected $isDefault;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->originalId = $id;
        $this->title = $title;
        $this->isDefault = true;
    }

    abstract public function toArray(): array;
    abstract public function getFilterField(Options $options);

    public function setOriginalId(string $id)
    {
        $this->originalId = $id;
    }

    /**
     * @param bool $isDefault
     */
    public function setDefault(bool $isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string)$this->id;
    }

    /**
     * @param Options $options
     * @return array
     */
    protected function getOptionsFilter(Options $options): array
    {
        $filterId = $options->getCurrentFilterId();
        $options = $options->getOptions();
        return $options['filters'][$filterId]['fields'] ?? [];
    }

    /**
     * @return string|string[]
     */
    public function getFilterData()
    {
        if ($this->id === $this->originalId) {
            return $this->id;
        }

        return [$this->id => $this->originalId];
    }
}