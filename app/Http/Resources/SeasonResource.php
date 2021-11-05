<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'begin' => $this->begin,
            'end' => $this->end,
            'public' => $this->public,
            'is_generated' => $this->is_generated,
            'allow_replacement' => $this->allow_replacement,
            'active' => $this->active,
            'group_id' => $this->group_id,
            'group' => new GroupResource($this->group),
            'admin_id' => $this->admin_id,
            'admin' => new UserPublicResource($this->admin),
            'day' => $this->day, 
            'start_hour' => $this->start_hour,
            'type' => $this->type,
            'season_draw' => $this->seasonDraw,
            'type_member' => $this->typeMember,
        ];
    }
}
