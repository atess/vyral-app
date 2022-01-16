<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TwitCollection extends ResourceCollection
{
    protected PaginationResource $pagination;

    public function __construct($resource)
    {
        $this->pagination = new PaginationResource($resource);
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'list' => TwitResource::collection($this->collection),
            'pagination' => $this->pagination,
        ];
    }
}
