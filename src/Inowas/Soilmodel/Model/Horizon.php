<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

use Inowas\Common\Conductivity\Conductivity;
use Inowas\Common\Length\HBottom;
use Inowas\Common\Length\HTop;
use Inowas\Common\Storage\Storage;

class Horizon
{
    /** @var HorizonId $id */
    protected $id;

    /** @var  HTop */
    protected $hTop;

    /** @var  HBottom */
    protected $hBot;

    /** @var  Conductivity */
    protected $conductivity;

    /** @var  Storage */
    protected $storage;

    public function id(): HorizonId
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return array(
            'id' => $this->id->toString(),
            'h_top' => $this->hTop->toMeters(),
            'h_bot' => $this->hBot->toMeters(),
            'conductivity' => $this->conductivity->toArray(),
            'storage' => $this->storage->toArray()
        );
    }

    public static function fromArray(array $data): Horizon
    {
        $self = new self();
        $self->id = HorizonId::fromString($data['id']);
        $self->hTop = HTop::fromMeters($data['h_top']);
        $self->hBot = HTop::fromMeters($data['h_bot']);
        $self->conductivity = Conductivity::fromArray($data['conductivity']);
        $self->storage = Storage::fromArray($data['storage']);
        return $self;
    }

    public static function fromParams(HorizonId $id, HTop $hTop, HBottom $hBot, Conductivity $cond, Storage $storage): Horizon
    {
        $self = new self();
        $self->id = $id;
        $self->hTop = $hTop;
        $self->hBot = $hBot;
        $self->conductivity = $cond;
        $self->storage = $storage;

        return $self;
    }
}
