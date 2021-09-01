<?php
namespace Bx\Model\UI\Fields;

use Bitrix\Main\UI\Filter\Options;
use function DI\string;

class BooleanFilterField extends BaseFilterField
{
    private $listLabels;
    private $listValues;

  /**
   * @param string $id
   * @param string $title
   */
  public function __construct(string $id, string $title)
    {
        parent::__construct($id, $title);

        $this->listLabels = [
          'Y' => 'Да',
          'N' => 'Нет'
        ];
        $this->listValues = [
          'Y' => true,
          'N' => false
        ];
    }

  /**
   * @param string $label
   * @param $value
   * @return BooleanFilterField
   */
  public function setTrueOption(string $label, $value = true): BooleanFilterField
  {
      $this->listLabels['Y'] = $label;
      $this->listValues['Y'] = $value;
      return $this;
    }

  /**
   * @param string $label
   * @param $value
   * @return BooleanFilterField
   */
  public function setFalseOption(string $label, $value = false): BooleanFilterField
  {
      $this->listLabels['N'] = $label;
      $this->listValues['N'] = $value;
      return $this;
    }



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'type' => 'list',
            'items' => $this->listLabels,
            'default' => $this->isDefault,
        ];
    }

    /**
     * @param Options $options
     * @return array
     */
    public function getFilterField(Options $options): array
    {
        $data = $this->getOptionsFilter($options);
        if (isset($this->listValues[$data[$this->id]])) {
          return [
                $this->id => $this->listValues[$data[$this->id]],
            ];
        }

        return [];
    }
}
