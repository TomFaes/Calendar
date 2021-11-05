<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SeasonPaginationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' =>  SeasonResource::collection($this->collection),
            'current_page' => $this->currentPage() ?? null,
            'last_page' => $this->lastPage() ?? null,
            'to' => $this->perPage() ?? null,
        ];
    }
}
