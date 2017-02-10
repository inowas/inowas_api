<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

class ModflowModelActiveCells
{
    /** @var array  */
    private $cells;

    public static function fromArray(array $cells)
    {
        return new self($cells);
    }

    private function __construct(array $cells)
    {
        $this->cells = $cells;
    }

    public static function fromObject($obj)
    {
        $cells = array();
        foreach ($obj as $row => $cols){
            foreach ($cols as $col => $value){
                $cells[intval($row)][intval($col)] = $value;
            }
        }

        return new self($cells);
    }

    public function cells(): array
    {
        if (!is_array($this->cells)) {
            $this->cells = [];
        }

        return $this->cells;
    }
}
