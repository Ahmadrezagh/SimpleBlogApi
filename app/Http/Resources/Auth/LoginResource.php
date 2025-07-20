<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $this->resource['user']->id,
                    'name' => $this->resource['user']->name,
                    'email' => $this->resource['user']->email,
                    'created_at' => $this->resource['user']->created_at,
                    'updated_at' => $this->resource['user']->updated_at,
                ],
                'token' => $this->resource['token'],
                'token_type' => 'Bearer'
            ]
        ];
    }
}
