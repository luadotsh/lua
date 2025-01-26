<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workspace_id' => $this->workspace_id,
            'domain' => $this->domain,
            'key' => $this->key,
            'url' => $this->url,
            'ios' => $this->ios,
            'android' => $this->android,
            'link' => $this->link,
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_term' => $this->utm_term,
            'utm_content' => $this->utm_content,
            'utm_name' => $this->utm_name,
            'clicks' => $this->clicks,
            'last_click' => $this->last_click,
            'external_id' => $this->external_id,
            'password' => $this->password,
            'expires_at' => $this->expires_at ? $this->expires_at->format('Y-m-d H:i:s') : null,
            'expired_redirect_url' => $this->expired_redirect_url,
            'tags' => TagResource::collection($this->tags),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
