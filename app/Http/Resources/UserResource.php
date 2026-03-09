<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'avatarUrl' => $this->avatar_url,
            'memberLevel' => $this->member_level,
        ];
    }
}
